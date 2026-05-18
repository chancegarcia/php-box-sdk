<?php

namespace Box\Event\Http;

readonly class RateLimitHit
{
    public function __construct(
        public int $retryAfter,
    ) {
    }
}
