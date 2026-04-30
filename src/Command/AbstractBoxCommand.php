<?php

namespace Box\Command;

use Box\Client;
use Box\Connection\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Psr\Log\LoggerInterface;
use Box\Logger\LoggerFactory;
use Exception;
use InvalidArgumentException;
use Box\Contract\BoxClientFactoryInterface;

abstract class AbstractBoxCommand extends Command
{
    protected LoggerInterface $logger;
    protected string $defaultLogConfig = 'config/monolog.php';

    public function __construct(
        protected BoxClientFactoryInterface $clientFactory,
        protected LoggerFactory $loggerFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('log-config', null, InputOption::VALUE_REQUIRED, 'Path to a different log config file')
            ->addOption('log-dir', null, InputOption::VALUE_REQUIRED, 'Different log directory')
            ->addOption('log-file', null, InputOption::VALUE_REQUIRED, 'Different base log file name (contains all levels)')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output result as JSON to console');

        $this->configureTransportOption();
    }

    protected function configureTransportOption(): void
    {
        $this->addOption(
            'transport',
            null,
            InputOption::VALUE_REQUIRED,
            sprintf('The HTTP transport to use. Allowed values: %s, %s', Connection::TRANSPORT_CURL, Connection::TRANSPORT_GUZZLE)
        );
    }

    protected function applyTransportOption(InputInterface $input, Client $client): void
    {
        $transport = $input->getOption('transport');
        if (null === $transport) {
            return;
        }

        $allowedTransports = [Connection::TRANSPORT_CURL, Connection::TRANSPORT_GUZZLE];
        if (!in_array($transport, $allowedTransports, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid transport "%s". Allowed transports: %s.', $transport, implode(', ', $allowedTransports))
            );
        }

        $client->getConnection()->setTransportName($transport);
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
