<?php

namespace Box\Command;

use Box\Auth\Jwt\JwtAuthConfig;
use Box\Auth\Jwt\JwtProviderInterface;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Exception\BoxException;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class JwtTokenCommand extends AbstractBoxCommand
{
    protected static $defaultName = 'box:jwt:token';

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
            ->setName(self::$defaultName)
            ->setDescription('Exchange a JWT assertion for a Box access token (enterprise or app user).')
            ->addOption('user-id', null, InputOption::VALUE_REQUIRED, 'User ID for App User token exchange');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logger->info('Starting JWT token exchange command');

        try {
            $config = new JwtAuthConfig(
                clientId:             $this->configProvider->getJwtClientId(),
                clientSecret:         $this->configProvider->getJwtClientSecret(),
                enterpriseId:         $this->configProvider->getJwtEnterpriseId(),
                publicKeyId:          $this->configProvider->getJwtPublicKeyId(),
                privateKey:           $this->configProvider->getJwtPrivateKey(),
                privateKeyPassphrase: $this->configProvider->getJwtPrivateKeyPassphrase(),
            );

            $client = $this->clientFactory->createJwtClient($config);
            $this->applyStorageOption($input, $client);

            $provider = $client->getAuthProvider();
            if (!$provider instanceof JwtProviderInterface) {
                throw new BoxException('Auth provider is not an instance of JwtProviderInterface');
            }

            $userId = $input->getOption('user-id');

            if ($userId) {
                $io->comment(sprintf('Exchanging JWT for App User token (User ID: %s)...', $userId));
                $token = $provider->exchangeForAppUserToken($userId);
            } else {
                $io->comment('Exchanging JWT for Enterprise token...');
                $token = $provider->exchangeForEnterpriseToken();
            }

            $tokenData = $token->toArray();

            if ($input->getOption('json')) {
                $this->outputFormatter->formatMasked($io, [
                    'success' => true,
                    'command' => self::$defaultName,
                    'message' => 'JWT token exchange successful',
                    'data' => $tokenData,
                ], true);
            } else {
                $io->success('JWT token exchange successful!');
                $this->outputFormatter->formatMasked($io, $tokenData);
            }

            $this->logger->info('JWT token exchange completed successfully');
            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error($e->getMessage());
            $this->logger->error('JWT token exchange failed', ['exception' => $e]);
            return Command::FAILURE;
        }
    }
}
