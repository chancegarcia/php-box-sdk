<?php

namespace Box\Command;

use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Logger\LoggerFactory;
use Box\Service\ConsoleOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AuthUrlCommand extends AbstractBoxCommand
{
    protected static $defaultName = 'box:auth:url';

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
            ->setDescription('Builds and prints the Box OAuth2 authorization URL')
            ->setHelp('This command generates the URL you need to visit in your browser to authorize this application.')
            ->addOption('redirect-uri', null, InputOption::VALUE_REQUIRED, 'Optional redirect URI')
            ->addOption('state', null, InputOption::VALUE_REQUIRED, 'Optional state parameter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logger->info('Generating authorization URL');
        
        $client = $this->clientFactory->createClient();

        $redirectUri = $input->getOption('redirect-uri') ?? $this->configProvider->getRedirectUri();
        if ($redirectUri) {
            $client->setRedirectUri($redirectUri);
        }

        $state = $input->getOption('state') ?? $this->configProvider->getState();
        if ($state) {
            $client->setState($state);
        }

        $url = $client->buildAuthQuery();

        if ($input->getOption('json')) {
            $this->outputFormatter->formatMasked($io, [
                'success' => true,
                'command' => self::$defaultName,
                'message' => 'Authorization URL generated successfully',
                'data' => [
                    'url' => $url,
                    'client_id' => $client->getClientId(),
                    'redirect_uri' => $client->getRedirectUri(),
                    'state' => $client->getState(),
                ],
            ], true);
        } else {
            $io->title('Box Authorization URL');
            $io->text('Visit the following URL in your browser to authorize the application:');
            $io->newLine();
            $io->writeln($url);
            $io->newLine();
            $io->section('Resolved Configuration');
            $this->outputFormatter->formatMasked($io, [
                'client_id' => $client->getClientId(),
                'redirect_uri' => $client->getRedirectUri(),
                'state' => $client->getState(),
            ]);
        }

        $this->logger->info('Authorization URL generated', ['url' => $url]);
        return Command::SUCCESS;
    }
}
