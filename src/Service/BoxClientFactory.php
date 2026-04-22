<?php

namespace Box\Service;

use Box\Client;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;

class BoxClientFactory implements BoxClientFactoryInterface
{
    public function __construct(
        private ConfigProviderInterface $configProvider
    ) {
    }

    public function createClient(): Client
    {
        $client = new Client();
        $client->setClientId($this->configProvider->getClientId());
        $client->setClientSecret($this->configProvider->getClientSecret());

        $redirectUri = $this->configProvider->getRedirectUri();
        if (null !== $redirectUri) {
            $client->setRedirectUri($redirectUri);
        }

        $state = $this->configProvider->getState();
        if (null !== $state) {
            $client->setState($state);
        }

        return $client;
    }
}
