<?php

namespace Box\Trait;

use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    protected ?LoggerInterface $logger = null;

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(?LoggerInterface $logger = null): void
    {
        $this->logger = $logger;
    }
}
