<?php

namespace Box\Command;

use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Service\ConsoleOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;

class AuthExchangeCommand extends Command
{
    protected static $defaultName = 'box:auth:exchange-code';

    public function __construct(
        private BoxClientFactoryInterface $clientFactory,
        private ConfigProviderInterface $configProvider,
        private ConsoleOutputFormatter $outputFormatter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Exchanges an authorization code for an access token')
            ->setHelp('This command exchanges the temporary authorization code obtained from the browser for a more permanent access token.')
            ->addArgument('code', InputArgument::OPTIONAL, 'The authorization code (falls back to BOX_AUTH_CODE env)')
            ->addOption('secrets-file', null, InputOption::VALUE_REQUIRED, 'Path to write the full token payload')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force writing to secrets file without confirmation')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output masked result as JSON to console');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $client = $this->clientFactory->createClient();

        $code = $input->getArgument('code') ?? $this->configProvider->getAuthCode();

        if (!$code) {
            $io->error('Authorization code is required. Provide it as an argument or set BOX_AUTH_CODE env.');
            return Command::FAILURE;
        }

        $client->setAuthorizationCode($code);

        try {
            $io->comment('Exchanging code for token...');
            $token = $client->getAccessToken();
            $tokenData = $token->toBoxArray();

            if ($secretsPath = $input->getOption('secrets-file')) {
                if (!$input->getOption('force')) {
                    if (!$io->confirm(sprintf('Are you sure you want to write sensitive tokens to %s?', $secretsPath), false)) {
                        $io->warning('Secret export cancelled.');
                    } else {
                        $this->writeSecrets($secretsPath, $tokenData);
                        $io->success(sprintf('Secrets written to %s', $secretsPath));
                    }
                } else {
                    $this->writeSecrets($secretsPath, $tokenData);
                    $io->success(sprintf('Secrets written to %s', $secretsPath));
                }
            }

            if ($input->getOption('json')) {
                $this->outputFormatter->formatMasked($io, [
                    'success' => true,
                    'command' => self::$defaultName,
                    'message' => 'Token exchange successful',
                    'data' => $tokenData,
                ], true);
            } else {
                $io->success('Token exchange successful!');
                $this->outputFormatter->formatMasked($io, $tokenData);
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to exchange code: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function writeSecrets(string $path, array $data): void
    {
        $dir = dirname($path);
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new Exception(sprintf('The directory "%s" is not writable.', $dir));
        }

        if (false === file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR))) {
            throw new Exception(sprintf('Failed to write to file "%s".', $path));
        }
    }
}
