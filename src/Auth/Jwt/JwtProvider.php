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

    /**
     * @param array<string, mixed> $options
     *
     * @throws BoxException always — JWT does not support browser-based authorization flows
     * @return string
     */
    public function buildAuthorizationUrl(array $options = []): string
    {
        throw new BoxException('JWT authentication does not support browser-based authorization flows.');
    }

    /**
     * @param string $code
     *
     * @throws BoxException always — JWT does not use authorization codes
     * @return TokenInterface
     */
    public function exchangeAuthorizationCode(string $code): TokenInterface
    {
        throw new BoxException('JWT authentication does not use authorization codes.');
    }

    /**
     * @param TokenInterface $token
     * @param array<string, mixed> $options
     *
     * @throws BoxException
     * @return TokenInterface
     */
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

    /**
     * @throws BoxException
     * @return TokenInterface
     */
    public function exchangeForEnterpriseToken(): TokenInterface
    {
        $token = $this->exchangeAssertion($this->config->enterpriseId, 'enterprise');

        $this->lastSubjectId = $this->config->enterpriseId;
        $this->lastSubjectType = 'enterprise';

        return $token;
    }

    /**
     * @param string $userId
     *
     * @throws BoxException
     * @return TokenInterface
     */
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
