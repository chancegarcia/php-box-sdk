<?php

namespace Box\Service;

use Box\Auth\Jwt\JwtAuthConfig;
use Box\Auth\Jwt\JwtAssertionGenerator;
use Box\Auth\Jwt\JwtProvider;
use Box\Client;
use Box\ClientConfig;
use Box\Contract\BoxClientFactoryInterface;
use Box\Contract\ConfigProviderInterface;
use Box\Factory\TokenFactory;
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
        $config->setOAuth2ClientId($this->configProvider->getOAuth2ClientId());
        $config->setOAuth2ClientSecret($this->configProvider->getOAuth2ClientSecret());
        $config->setOAuth2RedirectUri($this->configProvider->getOAuth2RedirectUri());
        $config->setOAuth2AuthCode($this->configProvider->getOAuth2AuthCode());
        $config->setOAuth2State($this->configProvider->getOAuth2State());

        $client = new Client($config);

        $accessToken = $this->configProvider->getOAuth2AccessToken();
        $refreshToken = $this->configProvider->getOAuth2RefreshToken();
        if (null !== $accessToken) {
            $token = (new TokenFactory())->createToken();
            $token->setAccessToken($accessToken);
            if (null !== $refreshToken) {
                $token->setRefreshToken($refreshToken);
            }
            $client->setToken($token);
        }

        if ($this->logger) {
            $client->setLogger($this->logger);
        }

        return $client;
    }

    public function createJwtClient(JwtAuthConfig $config): Client
    {
        $client = new Client();
        $assertionGenerator = new JwtAssertionGenerator();
        $provider = new JwtProvider(
            $client->getConnection(),
            $client->getTokenFactory(),
            $config,
            $assertionGenerator
        );

        $client->setAuthProvider($provider);

        if ($this->logger) {
            $client->setLogger($this->logger);
        }

        return $client;
    }

    public function createClientForCurrentMode(): Client
    {
        if ('jwt' === $this->configProvider->getAuthMode()) {
            $config = new JwtAuthConfig(
                clientId:             $this->configProvider->getJwtClientId(),
                clientSecret:         $this->configProvider->getJwtClientSecret(),
                enterpriseId:         $this->configProvider->getJwtEnterpriseId(),
                publicKeyId:          $this->configProvider->getJwtPublicKeyId(),
                privateKey:           $this->configProvider->getJwtPrivateKey(),
                privateKeyPassphrase: $this->configProvider->getJwtPrivateKeyPassphrase(),
            );

            return $this->createJwtClient($config);
        }

        return $this->createClient();
    }
}
