<?php

namespace Box\Auth;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Factory\TokenFactoryInterface;
use Box\Exception\BoxException;

class OAuth2Provider implements OAuth2ProviderInterface
{
    public const string AUTH_URI = 'https://account.box.com/api/oauth2/authorize';

    public function __construct(
        protected ConnectionInterface $connection,
        protected TokenFactoryInterface $tokenFactory,
        protected ?string $clientId = null,
        protected ?string $clientSecret = null,
        protected ?string $redirectUri = null
    ) {
    }

    public function buildAuthorizationUrl(array $options = []): string
    {
        $params = array_merge([
            'response_type' => 'code',
            'client_id' => $this->clientId,
        ], $options);

        if (null !== $this->redirectUri && !isset($params['redirect_uri'])) {
            $params['redirect_uri'] = $this->redirectUri;
        }

        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        return self::AUTH_URI . '?' . $query;
    }

    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setClientSecret(?string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function setRedirectUri(?string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @throws BoxException
     */
    public function exchangeAuthorizationCode(string $code): TokenInterface
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        if (null !== $this->redirectUri) {
            $params['redirect_uri'] = $this->redirectUri;
        }

        $response = $this->connection->post(self::TOKEN_URI, $params);
        $data = $response->json();

        if (!is_array($data)) {
            throw new BoxException('Invalid response from Box API during token exchange', BoxException::INVALID_INPUT);
        }

        return $this->tokenFactory->createToken($data);
    }

    /**
     * @throws BoxException
     */
    public function refreshToken(TokenInterface $token, array $options = []): TokenInterface
    {
        $params = array_merge([
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->getRefreshToken(),
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ], $options);

        $response = $this->connection->post(self::TOKEN_URI, $params);
        $data = $response->json();

        if (!is_array($data)) {
            throw new BoxException('Invalid response from Box API during token refresh', BoxException::INVALID_INPUT);
        }

        return $this->tokenFactory->createToken($data);
    }

    public function revokeToken(TokenInterface $token): void
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'token' => $token->getAccessToken(),
        ];

        $this->connection->post(self::REVOKE_URI, $params);
    }
}
