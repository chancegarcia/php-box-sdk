<?php

namespace Box\Webhook;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class WebhookVerifier implements WebhookVerifierInterface
{
    private const MAX_AGE_SECONDS = 600;

    public function __construct(
        private readonly ?string $primaryKey = null,
        private readonly ?string $secondaryKey = null,
        private readonly int $maxAgeSeconds = self::MAX_AGE_SECONDS,
    ) {
        if ($this->primaryKey === null && $this->secondaryKey === null) {
            throw new \InvalidArgumentException('At least one webhook signing key (primary or secondary) must be provided.');
        }
    }

    public function verify(
        string $body,
        string $deliveryTimestamp,
        ?string $primarySignature = null,
        ?string $secondarySignature = null,
    ): bool {
        if (!$this->isTimestampFresh($deliveryTimestamp)) {
            return false;
        }

        // Box signs: HMAC-SHA256(body + delivery_timestamp, key), base64-encoded
        $payload = $body . $deliveryTimestamp;

        if ($this->primaryKey !== null && $primarySignature !== null) {
            if (hash_equals($this->computeSignature($payload, $this->primaryKey), $primarySignature)) {
                return true;
            }
        }

        if ($this->secondaryKey !== null && $secondarySignature !== null) {
            if (hash_equals($this->computeSignature($payload, $this->secondaryKey), $secondarySignature)) {
                return true;
            }
        }

        return false;
    }

    private function computeSignature(string $payload, string $key): string
    {
        return base64_encode(hash_hmac('sha256', $payload, $key, true));
    }

    private function isTimestampFresh(string $deliveryTimestamp): bool
    {
        $delivered = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339_EXTENDED, $deliveryTimestamp)
            ?: DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339, $deliveryTimestamp);

        if ($delivered === false) {
            return false;
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        return abs($now->getTimestamp() - $delivered->getTimestamp()) <= $this->maxAgeSeconds;
    }
}
