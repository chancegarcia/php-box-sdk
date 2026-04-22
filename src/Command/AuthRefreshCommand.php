<?php

namespace Box\Command;

use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Logger\LoggerFactory;
use Box\Model\Connection\Token\Token;
use Box\Service\ConsoleOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;

class AuthRefreshCommand extends AbstractBoxCommand
{
    protected static $defaultName = 'box:auth:refresh-token';

    public function __construct(
        private BoxClientFactoryInterface $clientFactory,
        private ConfigProviderInterface $configProvider,
        private ConsoleOutputFormatter $outputFormatter,
        LoggerFactory $loggerFactory
    ) {
        parent::__construct($loggerFactory);
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName(self::$defaultName)
            ->setDescription('Refreshes an access token using a refresh token')
            ->setHelp('This command uses a refresh token to obtain a new access token and a new refresh token.')
            ->addOption('secrets-file', null, InputOption::VALUE_REQUIRED, 'Path to write the full token payload')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force writing to secrets file without confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logger->info('Starting token refresh command');
        
        $client = $this->clientFactory->createClient();

        $refreshTokenValue = $this->configProvider->getRefreshToken();

        if (!$refreshTokenValue) {
            $io->error('Refresh token is required. Set BOX_REFRESH_TOKEN env.');
            $this->logger->error('Refresh token is missing');
            return Command::FAILURE;
        }

        $token = new Token();
        $token->setRefreshToken($refreshTokenValue);
        $client->setToken($token);

        try {
            $io->comment('Refreshing token...');
            $newToken = $client->refreshToken();
            $tokenData = $newToken->toBoxArray();

            if ($secretsPath = $input->getOption('secrets-file')) {
                $this->writeSecrets($secretsPath, $tokenData, $io, (bool)$input->getOption('force'));
            }

            if ($input->getOption('json')) {
                $this->outputFormatter->formatMasked($io, [
                    'success' => true,
                    'command' => self::$defaultName,
                    'message' => 'Token refresh successful',
                    'data' => $tokenData,
                ], true);
            } else {
                $io->success('Token refresh successful!');
                $this->outputFormatter->formatMasked($io, $tokenData);
            }

            $this->logger->info('Token refresh completed successfully');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to refresh token: ' . $e->getMessage());
            $this->logger->error('Failed to refresh token', ['exception' => $e]);
            return Command::FAILURE;
        }
    }
}
