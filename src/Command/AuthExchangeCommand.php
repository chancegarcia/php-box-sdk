<?php

namespace Box\Command;

use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Exception\BoxResponseException;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;

class AuthExchangeCommand extends AbstractBoxCommand
{
    protected static $defaultName = 'box:auth:exchange-code';

    public function __construct(
        BoxClientFactoryInterface $clientFactory,
        private ConfigProviderInterface $configProvider,
        private ConsoleOutputFormatter $outputFormatter,
        LoggerFactory $loggerFactory
    ) {
        parent::__construct($clientFactory, $loggerFactory);
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName(self::$defaultName)
            ->setDescription('Exchanges an authorization code for an access token')
            ->setHelp('This command exchanges the temporary authorization code obtained from the browser for a more permanent access token.')
            ->addArgument('code', InputArgument::OPTIONAL, 'The authorization code (falls back to BOX_AUTH_CODE env)')
            ->addOption('secrets-file', null, InputOption::VALUE_REQUIRED, 'Path to write the full token payload')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force writing to secrets file without confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logger->info('Starting token exchange command');

        $client = $this->clientFactory->createClient();
        $this->applyTransportOption($input, $client);

        $code = $input->getArgument('code') ?? $this->configProvider->getAuthCode();

        if (!$code) {
            $io->error('Authorization code is required. Provide it as an argument or set BOX_AUTH_CODE env.');
            $this->logger->error('Authorization code is missing');
            return Command::FAILURE;
        }

        $client->setAuthorizationCode($code);

        try {
            $io->comment('Exchanging code for token...');
            $token = $client->getAccessToken();
            $tokenData = $token->toArray();

            if ($secretsPath = $input->getOption('secrets-file')) {
                $this->writeSecrets($secretsPath, $tokenData, $io, (bool)$input->getOption('force'));
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

            $this->logger->info('Token exchange completed successfully');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $message = 'Failed to exchange code: ' . $e->getMessage();
            if ($e instanceof BoxResponseException) {
                if ($e->getBoxCode()) {
                    $message .= " (Box Code: " . $e->getBoxCode() . ")";
                }
                if ($e->getErrorDescription()) {
                    $message .= "\nDescription: " . $e->getErrorDescription();
                }
            }
            $io->error($message);
            $this->logger->error('Failed to exchange code', ['exception' => $e]);
            return Command::FAILURE;
        }
    }
}
