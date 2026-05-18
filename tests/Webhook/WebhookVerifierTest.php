<?php

namespace Box\Tests\Webhook;

use Box\Webhook\WebhookVerifier;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class WebhookVerifierTest extends TestCase
{
    private const PRIMARY_KEY = 'test-primary-signing-key';
    private const SECONDARY_KEY = 'test-secondary-signing-key';

    private function freshTimestamp(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::RFC3339);
    }

    private function signature(string $body, string $timestamp, string $key): string
    {
        return base64_encode(hash_hmac('sha256', $body . $timestamp, $key, true));
    }

    public function testVerifiesValidPrimarySignature(): void
    {
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = $this->freshTimestamp();

        $this->assertTrue($verifier->verify(
            body: $body,
            deliveryTimestamp: $timestamp,
            primarySignature: $this->signature($body, $timestamp, self::PRIMARY_KEY),
        ));
    }

    public function testVerifiesValidSecondarySignature(): void
    {
        $verifier = new WebhookVerifier(secondaryKey: self::SECONDARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = $this->freshTimestamp();

        $this->assertTrue($verifier->verify(
            body: $body,
            deliveryTimestamp: $timestamp,
            secondarySignature: $this->signature($body, $timestamp, self::SECONDARY_KEY),
        ));
    }

    public function testVerifiesWhenBothKeysConfiguredAndPrimaryMatches(): void
    {
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY, secondaryKey: self::SECONDARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = $this->freshTimestamp();

        $this->assertTrue($verifier->verify(
            body: $body,
            deliveryTimestamp: $timestamp,
            primarySignature: $this->signature($body, $timestamp, self::PRIMARY_KEY),
            secondarySignature: 'wrong-signature',
        ));
    }

    public function testVerifiesWhenBothKeysConfiguredAndSecondaryMatches(): void
    {
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY, secondaryKey: self::SECONDARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = $this->freshTimestamp();

        $this->assertTrue($verifier->verify(
            body: $body,
            deliveryTimestamp: $timestamp,
            primarySignature: 'wrong-signature',
            secondarySignature: $this->signature($body, $timestamp, self::SECONDARY_KEY),
        ));
    }

    public function testReturnsFalseForWrongSignature(): void
    {
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = $this->freshTimestamp();

        $this->assertFalse($verifier->verify(
            body: $body,
            deliveryTimestamp: $timestamp,
            primarySignature: 'aGVsbG8td29ybGQ=',
        ));
    }

    public function testReturnsFalseWhenSignaturesOmitted(): void
    {
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY, secondaryKey: self::SECONDARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = $this->freshTimestamp();

        $this->assertFalse($verifier->verify(body: $body, deliveryTimestamp: $timestamp));
    }

    public function testReturnsFalseForStaleTimestamp(): void
    {
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $staleTimestamp = (new DateTimeImmutable('-20 minutes', new DateTimeZone('UTC')))
            ->format(DateTimeInterface::RFC3339);

        $this->assertFalse($verifier->verify(
            body: $body,
            deliveryTimestamp: $staleTimestamp,
            primarySignature: $this->signature($body, $staleTimestamp, self::PRIMARY_KEY),
        ));
    }

    public function testReturnsFalseForUnparsableTimestamp(): void
    {
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';

        $this->assertFalse($verifier->verify(
            body: $body,
            deliveryTimestamp: 'not-a-timestamp',
            primarySignature: 'anything',
        ));
    }

    public function testThrowsWhenNoKeysProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new WebhookVerifier();
    }

    public function testCustomMaxAgeIsRespected(): void
    {
        // A signature from 30 seconds ago should pass with maxAgeSeconds=60
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY, maxAgeSeconds: 60);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = (new DateTimeImmutable('-30 seconds', new DateTimeZone('UTC')))
            ->format(DateTimeInterface::RFC3339);

        $this->assertTrue($verifier->verify(
            body: $body,
            deliveryTimestamp: $timestamp,
            primarySignature: $this->signature($body, $timestamp, self::PRIMARY_KEY),
        ));
    }

    public function testPrimarySignatureKeyMismatchDoesNotMatchSecondary(): void
    {
        // Primary key present, secondary absent; signature computed with a wrong key returns false
        $verifier = new WebhookVerifier(primaryKey: self::PRIMARY_KEY);
        $body = '{"type":"FILE.UPLOADED"}';
        $timestamp = $this->freshTimestamp();

        $this->assertFalse($verifier->verify(
            body: $body,
            deliveryTimestamp: $timestamp,
            primarySignature: $this->signature($body, $timestamp, self::SECONDARY_KEY),
        ));
    }
}
