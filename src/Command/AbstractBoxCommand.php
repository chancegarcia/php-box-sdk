<?php

namespace Box\Command;

use Box\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Psr\Log\LoggerInterface;
use Box\Logger\LoggerFactory;
use Box\Dto\TokenStorageContext;
use Box\Storage\Token\Filesystem\FilesystemTokenStorage;
use Box\Storage\Token\Pdo\TokenStorage as PdoTokenStorage;
use Exception;
use InvalidArgumentException;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;

abstract class AbstractBoxCommand extends Command
{
    protected LoggerInterface $logger;
    protected string $defaultLogConfig = 'config/monolog.php';

    public function __construct(
        protected BoxClientFactoryInterface $clientFactory,
        protected LoggerFactory $loggerFactory,
        protected ConfigProviderInterface $configProvider
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('log-config', null, InputOption::VALUE_REQUIRED, 'Path to a different log config file')
            ->addOption('log-dir', null, InputOption::VALUE_REQUIRED, 'Different log directory')
            ->addOption('log-file', null, InputOption::VALUE_REQUIRED, 'Different base log file name (contains all levels)')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output result as JSON to console')
            ->addOption('use-storage', null, InputOption::VALUE_NONE, 'Enable token storage')
            ->addOption('storage-type', null, InputOption::VALUE_REQUIRED, 'Type of storage (pdo, filesystem)', 'filesystem')
            ->addOption('user-id', null, InputOption::VALUE_REQUIRED, 'User ID for storage context')
            ->addOption('enterprise-id', null, InputOption::VALUE_REQUIRED, 'Enterprise ID for storage context')
            ->addOption('pdo-dsn', null, InputOption::VALUE_REQUIRED, 'PDO DSN for storage')
            ->addOption('pdo-user', null, InputOption::VALUE_REQUIRED, 'PDO username')
            ->addOption('pdo-pass', null, InputOption::VALUE_REQUIRED, 'PDO password')
            ->addOption('storage-path', null, InputOption::VALUE_REQUIRED, 'File path for filesystem token storage');
    }

    protected function applyStorageOption(InputInterface $input, Client $client): void
    {
        if (!$input->getOption('use-storage')) {
            return;
        }

        $userId = $input->getOption('user-id');
        $enterpriseId = $input->getOption('enterprise-id');
        $clientId = $client->getClientId();

        if (null === $userId && null === $enterpriseId) {
            $this->logger->warning('Storage enabled but no user-id or enterprise-id provided. Storage may not function correctly if context is required.');
        }

        $context = new TokenStorageContext(
            userId: $userId,
            enterpriseId: $enterpriseId,
            clientId: $clientId
        );

        $client->setTokenStorageContext($context);

        $storageType = $input->getOption('storage-type');

        $storage = match ($storageType) {
            'pdo' => $this->buildPdoStorage($input),
            'filesystem' => $this->buildFilesystemStorage($input),
            default => throw new InvalidArgumentException(
                sprintf('Unsupported storage type "%s". Valid types: pdo, filesystem.', $storageType)
            ),
        };

        $client->setTokenStorage($storage);

        $this->logger->info('Token storage configured for command', [
            'type' => $storageType,
            'user_id' => $userId,
            'enterprise_id' => $enterpriseId,
            'client_id' => $clientId
        ]);
    }

    private function buildPdoStorage(InputInterface $input): PdoTokenStorage
    {
        $dsn = $input->getOption('pdo-dsn') ?? $this->configProvider->getStoragePdoDsn();
        if (null === $dsn) {
            throw new InvalidArgumentException('PDO DSN is required when storage is enabled. Use --pdo-dsn or BOX_STORAGE_PDO_DSN env.');
        }
        $user = $input->getOption('pdo-user') ?? $this->configProvider->getStoragePdoUser();
        $pass = $input->getOption('pdo-pass') ?? $this->configProvider->getStoragePdoPassword();

        return new PdoTokenStorage($dsn, $user, $pass);
    }

    private function buildFilesystemStorage(InputInterface $input): FilesystemTokenStorage
    {
        $defaultPath = rtrim((string) getcwd(), '/') . '/var/tmp/box-sdk/tokens.json';

        $path = $input->getOption('storage-path') ?? $this->configProvider->getStorageFilePath() ?? $defaultPath;

        return new FilesystemTokenStorage($path);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $logConfigPath = $input->getOption('log-config') ?? $this->defaultLogConfig;

        if (!file_exists($logConfigPath)) {
             throw new Exception(sprintf('Log config file "%s" not found.', $logConfigPath));
        }

        $config = require $logConfigPath;

        $overrides = [];
        if ($logDir = $input->getOption('log-dir')) {
            $overrides['log_dir'] = $logDir;
        }
        if ($logFile = $input->getOption('log-file')) {
            $overrides['log_file'] = $logFile;
        }

        $this->logger = $this->loggerFactory->createLogger($config, $overrides);
        $this->clientFactory->setLogger($this->logger);
    }

    protected function writeSecrets(string $path, array $data, SymfonyStyle $io, bool $force = false): void
    {
        if (!$force) {
            if (!$io->confirm(sprintf('Are you sure you want to write sensitive tokens to %s?', $path), false)) {
                $io->warning('Secret export cancelled.');
                return;
            }
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                $this->logger->error('Failed to create directory for secrets', ['path' => $dir]);
                throw new Exception(sprintf('Directory "%s" was not created', $dir));
            }
        }

        if (!is_writable($dir)) {
            $this->logger->error('Directory for secrets is not writable', ['path' => $dir]);
            throw new Exception(sprintf('The directory "%s" is not writable.', $dir));
        }

        if (false === file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR))) {
            $this->logger->error('Failed to write secrets to file', ['path' => $path]);
            throw new Exception(sprintf('Failed to write to file "%s".', $path));
        }

        $io->success(sprintf('Secrets written to %s', $path));
        $this->logger->info('Secrets written successfully', ['path' => $path]);
    }
}
