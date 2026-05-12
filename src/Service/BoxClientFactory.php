<?php

namespace Box\Service;

use Box\Client;
use Box\ClientConfig;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Psr\Log\LoggerInterface;

class BoxClientFactory implements BoxClientFactoryInterface
{
    private ?LoggerInterface $logger = null;

    public function __construct(
        private ConfigProviderInterface $configProvider
    ) {
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function createClient(): Client
    {
        $config = new ClientConfig();
        $config->setClientId($this->configProvider->getClientId());
        $config->setClientSecret($this->configProvider->getClientSecret());
        $config->setRedirectUri($this->configProvider->getRedirectUri());
        $config->setAuthorizationCode($this->configProvider->getAuthCode());
        $config->setState($this->configProvider->getState());

        $client = new Client($config);
        if ($this->logger) {
            $client->setLogger($this->logger);
        }

        return $client;
    }
}
