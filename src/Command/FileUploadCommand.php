<?php

namespace Box\Command;

use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Exception\BoxResponseException;
use Box\Service\File\FileService;
use Box\Logger\LoggerFactory;
use Box\Connection\Token\Token;
use Box\Service\ConsoleOutputFormatter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;

#[AsCommand(name: 'box:file:upload', description: 'Uploads a local file to Box')]
class FileUploadCommand extends AbstractBoxCommand
{
    public function __construct(
        BoxClientFactoryInterface $clientFactory,
        ConfigProviderInterface $configProvider,
        private ConsoleOutputFormatter $outputFormatter,
        LoggerFactory $loggerFactory
    ) {
        parent::__construct($clientFactory, $loggerFactory, $configProvider);
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setHelp('This command uploads a file from your local system to Box.')
            ->addArgument(
                'file-path',
                InputArgument::OPTIONAL,
                'The local path to the file (falls back to BOX_UPLOAD_FILE_PATH env)'
            )
            ->addOption(
                'folder-id',
                null,
                InputOption::VALUE_REQUIRED,
                'Target folder ID (falls back to BOX_UPLOAD_FOLDER_ID env or 0)'
            );
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logger->info('Starting file upload command');

        $client = $this->clientFactory->createClient();

        $filePath = $input->getArgument('file-path') ?? $this->configProvider->getUploadFilePath();

        if (!$filePath) {
            $io->error('File path is required. Provide it as an argument or set BOX_UPLOAD_FILE_PATH env.');
            $this->logger->error('File path is missing');
            return Command::FAILURE;
        }

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $io->error(sprintf('The file "%s" does not exist or is not readable.', $filePath));
            $this->logger->error('File does not exist or is not readable', ['path' => $filePath]);
            return Command::FAILURE;
        }

        $folderId = $input->getOption('folder-id') ?? $this->configProvider->getUploadFolderId() ?? '0';
        $accessToken = $this->configProvider->getOAuth2AccessToken();

        if (empty($accessToken) || trim($accessToken) === '') {
            $io->error('BOX_ACCESS_TOKEN is required for upload.');
            $this->logger->error('Access token is missing for upload');
            return Command::FAILURE;
        }

        $token = new Token();
        $token->setAccessToken($accessToken);
        $client->setToken($token);

        try {
            $io->comment(sprintf('Uploading file "%s" to folder "%s"...', $filePath, $folderId));
            $this->logger->info('Uploading file', ['path' => $filePath, 'folder_id' => $folderId]);

            $connection = $client->getConnection();
            $connection->setAccessToken($accessToken);

            $response = $connection->postFile(FileService::UPLOAD_ENDPOINT, $filePath, (int)$folderId);
            $result = $client->parseResponse($response);

            if ($input->getOption('json')) {
                $this->outputFormatter->formatMasked($io, [
                    'success' => true,
                    'command' => $this->getName(),
                    'message' => 'File uploaded successfully',
                    'data' => $result,
                ], true);
            } else {
                $io->success('File uploaded successfully!');

                if (isset($result['entries'][0]['id'])) {
                    $fileId = $result['entries'][0]['id'];
                    $io->writeln(sprintf('<info>File ID</info>: %s', $fileId));
                    if (isset($result['entries'][0]['name'])) {
                        $io->writeln(sprintf('<info>Name</info>: %s', $result['entries'][0]['name']));
                    }
                    $subdomain = $this->getBoxSubdomain($input);
                    if (null !== $subdomain) {
                        $io->writeln(sprintf('<info>Box URL</info>: %s', FileService::buildWebUrl($fileId, $subdomain)));
                    }
                } else {
                    $this->outputFormatter->formatMasked($io, $result);
                }
            }

            $this->logger->info('File upload completed successfully', ['result' => $result]);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $message = 'Failed to upload file: ' . $e->getMessage();
            if ($e instanceof BoxResponseException) {
                if ($e->getBoxCode()) {
                    $message .= " (Box Code: " . $e->getBoxCode() . ")";
                }
                if ($e->getErrorDescription()) {
                    $message .= "\nDescription: " . $e->getErrorDescription();
                }
                $retryAfter = $e->getContext('retry_after_header');
                if ($retryAfter) {
                    $message .= "\nRetry After: " . $retryAfter;
                }
            }
            $io->error($message);
            $this->logger->error('Failed to upload file', ['exception' => $e]);
            return Command::FAILURE;
        }
    }
}
