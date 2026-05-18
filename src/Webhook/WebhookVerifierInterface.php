<?php

namespace Box\Webhook;

interface WebhookVerifierInterface
{
    /**
     * Verify an incoming Box webhook request signature.
     *
     * Returns true if the payload was signed by a configured key and the
     * delivery timestamp is within the allowed age window.
     */
    public function verify(
        string $body,
        string $deliveryTimestamp,
        ?string $primarySignature = null,
        ?string $secondarySignature = null,
    ): bool;
}
