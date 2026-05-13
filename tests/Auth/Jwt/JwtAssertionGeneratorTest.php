<?php

declare(strict_types=1);

namespace Box\Tests\Auth\Jwt;

use Box\Auth\Jwt\JwtAssertionGenerator;
use Box\Auth\Jwt\JwtAuthConfig;
use Box\Exception\BoxException;
use PHPUnit\Framework\TestCase;

class JwtAssertionGeneratorTest extends TestCase
{
    private string $privateKey;
    private string $publicKey;

    protected function setUp(): void
    {
        $keyResource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $pk = '';
        openssl_pkey_export($keyResource, $pk);
        $this->privateKey = $pk;
        $publicKeyDetails = openssl_pkey_get_details($keyResource);
        $this->publicKey = $publicKeyDetails['key'];
    }

    public function testGenerateReturnsValidJwtStructure(): void
    {
        $config = new JwtAuthConfig(
            'client_id',
            'secret',
            'ent_id',
            'pk_id',
            $this->privateKey
        );
        $generator = new JwtAssertionGenerator();
        $assertion = $generator->generate($config, 'sub_id', 'enterprise');

        $segments = explode('.', $assertion);
        $this->assertCount(3, $segments);

        $header = json_decode($this->base64UrlDecode($segments[0]), true);
        $this->assertSame('RS256', $header['alg']);
        $this->assertSame('JWT', $header['typ']);
        $this->assertSame('pk_id', $header['kid']);

        $payload = json_decode($this->base64UrlDecode($segments[1]), true);
        $this->assertSame('client_id', $payload['iss']);
        $this->assertSame('sub_id', $payload['sub']);
        $this->assertSame('enterprise', $payload['box_sub_type']);
        $this->assertSame('https://api.box.com/oauth2/token', $payload['aud']);
        $this->assertArrayHasKey('jti', $payload);
        $this->assertArrayHasKey('exp', $payload);

        $signature = $this->base64UrlDecode($segments[2]);
        $dataToVerify = $segments[0] . '.' . $segments[1];
        $this->assertSame(1, openssl_verify($dataToVerify, $signature, $this->publicKey, OPENSSL_ALGO_SHA256));
    }

    public function testGenerateWithUserSubjectType(): void
    {
        $config = new JwtAuthConfig(
            'client_id',
            'secret',
            'ent_id',
            'pk_id',
            $this->privateKey
        );
        $generator = new JwtAssertionGenerator();
        $assertion = $generator->generate($config, 'user_id', 'user');

        $segments = explode('.', $assertion);
        $payload = json_decode($this->base64UrlDecode($segments[1]), true);
        $this->assertSame('user', $payload['box_sub_type']);
        $this->assertSame('user_id', $payload['sub']);
    }

    public function testGenerateWithInvalidSubjectTypeThrowsException(): void
    {
        $config = new JwtAuthConfig(
            'client_id',
            'secret',
            'ent_id',
            'pk_id',
            $this->privateKey
        );
        $generator = new JwtAssertionGenerator();

        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Invalid subject type: admin. Must be exactly "enterprise" or "user".');

        $generator->generate($config, 'id', 'admin');
    }

    public function testGenerateWithBadPrivateKeyThrowsException(): void
    {
        $config = new JwtAuthConfig(
            'client_id',
            'secret',
            'ent_id',
            'pk_id',
            'bad_private_key'
        );
        $generator = new JwtAssertionGenerator();

        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('The private key could not be loaded.');

        $generator->generate($config, 'id', 'user');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return (string) base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
