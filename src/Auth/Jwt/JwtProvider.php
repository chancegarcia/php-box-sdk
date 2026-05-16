<?php

namespace Box\Auth\Jwt;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Event\Auth\JwtTokenGenerated;
use Box\Exception\BoxException;
use Box\Factory\TokenFactoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class JwtProvider implements JwtProviderInterface
{
    private ?string $lastSubjectId = null;
    private ?string $lastSubjectType = null;

    private ?EventDispatcherInterface $eventDispatcher = null;

    public function __construct(
        protected ConnectionInterface $connection,
        protected TokenFactoryInterface $tokenFactory,
        protected JwtAuthConfig $config,
        protected JwtAssertionGeneratorInterface $assertionGenerator
    ) {
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->eventDispatcher = $dispatcher;
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function buildAuthorizationUrl(array $options = []): string
    {
        throw new BoxException('JWT authentication does not support browser-based authorization flows.');
    }

    public function exchangeAuthorizationCode(string $code): TokenInterface
    {
        throw new BoxException('JWT authentication does not use authorization codes.');
    }

    public function refreshToken(TokenInterface $token, array $options = []): TokenInterface
    {
        return $this->exchangeAssertion(
            $this->lastSubjectId ?? $this->config->enterpriseId,
            $this->lastSubjectType ?? 'enterprise'
        );
    }

    public function revokeToken(TokenInterface $token): void
    {
        $params = [
            'client_id' => $this->config->clientId,
            'client_secret' => $this->config->clientSecret,
            'token' => $token->getAccessToken(),
        ];

        $this->connection->post(self::REVOKE_URI, $params);
    }

    public function exchangeForEnterpriseToken(): TokenInterface
    {
        $token = $this->exchangeAssertion($this->config->enterpriseId, 'enterprise');

        $this->lastSubjectId = $this->config->enterpriseId;
        $this->lastSubjectType = 'enterprise';

        return $token;
    }

    public function exchangeForAppUserToken(string $userId): TokenInterface
    {
        $token = $this->exchangeAssertion($userId, 'user');

        $this->lastSubjectId = $userId;
        $this->lastSubjectType = 'user';

        return $token;
    }

    private function exchangeAssertion(string $subjectId, string $subjectType): TokenInterface
    {
        $assertion = $this->assertionGenerator->generate($this->config, $subjectId, $subjectType);

        $params = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $assertion,
            'client_id' => $this->config->clientId,
            'client_secret' => $this->config->clientSecret,
        ];

        $response = $this->connection->post(self::TOKEN_URI, $params);
        $data = $response->json();

        if (!is_array($data)) {
            throw new BoxException('Invalid response from Box API during JWT assertion exchange');
        }

        $token = $this->tokenFactory->createToken($data);
        $this->eventDispatcher?->dispatch(new JwtTokenGenerated($token));

        return $token;
    }
}
