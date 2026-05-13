<?php

declare(strict_types=1);

namespace Box\Tests\Auth\Jwt;

use Box\Auth\Jwt\JwtAuthConfig;
use Box\Exception\BoxException;
use PHPUnit\Framework\TestCase;

class JwtAuthConfigTest extends TestCase
{
    public function testConstructingWithEmptyClientIdThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Client ID cannot be empty.');

        new JwtAuthConfig('', 'secret', 'ent_id', 'pk_id', 'pk_content');
    }

    public function testConstructingWithEmptyEnterpriseIdThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Enterprise ID cannot be empty.');

        new JwtAuthConfig('client_id', 'secret', '', 'pk_id', 'pk_content');
    }

    public function testConstructingWithEmptyPublicKeyIdThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Public Key ID cannot be empty.');

        new JwtAuthConfig('client_id', 'secret', 'ent_id', '', 'pk_content');
    }

    public function testConstructingWithEmptyPrivateKeyThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Private Key cannot be empty.');

        new JwtAuthConfig('client_id', 'secret', 'ent_id', 'pk_id', '');
    }

    public function testFullyValidConfigConstructsWithoutError(): void
    {
        $config = new JwtAuthConfig(
            'client_id',
            'secret',
            'ent_id',
            'pk_id',
            'pk_content',
            'passphrase',
            'RS256'
        );

        $this->assertSame('client_id', $config->clientId);
        $this->assertSame('secret', $config->clientSecret);
        $this->assertSame('ent_id', $config->enterpriseId);
        $this->assertSame('pk_id', $config->publicKeyId);
        $this->assertSame('pk_content', $config->privateKey);
        $this->assertSame('passphrase', $config->privateKeyPassphrase);
        $this->assertSame('RS256', $config->jwtAlgorithm);
    }
}
