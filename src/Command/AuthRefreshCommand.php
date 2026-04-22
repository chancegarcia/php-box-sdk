<?php

namespace Box\Command;

use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Model\Connection\Token\Token;
use Box\Service\ConsoleOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;

class AuthRefreshCommand extends Command
{
    protected static $defaultName = 'box:auth:refresh-token';

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
            ->setDescription('Refreshes an access token using a refresh token')
            ->setHelp('This command uses a refresh token to obtain a new access token and a new refresh token.')
            ->addOption('refresh-token', null, InputOption::VALUE_REQUIRED, 'The refresh token (falls back to BOX_REFRESH_TOKEN env)')
            ->addOption('secrets-file', null, InputOption::VALUE_REQUIRED, 'Path to write the full token payload')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force writing to secrets file without confirmation')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output masked result as JSON to console');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $client = $this->clientFactory->createClient();

        $refreshTokenValue = $input->getOption('refresh-token') ?? $this->configProvider->getRefreshToken();

        if (!$refreshTokenValue) {
            $io->error('Refresh token is required. Provide it as an option or set BOX_REFRESH_TOKEN env.');
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
                    'message' => 'Token refresh successful',
                    'data' => $tokenData,
                ], true);
            } else {
                $io->success('Token refresh successful!');
                $this->outputFormatter->formatMasked($io, $tokenData);
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to refresh token: ' . $e->getMessage());
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
