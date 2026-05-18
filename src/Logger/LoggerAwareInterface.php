<?php

namespace Box\Logger;

use Psr\Log\LoggerAwareInterface as PsrLoggerAwareInterface;
use Psr\Log\LoggerInterface;

interface LoggerAwareInterface extends PsrLoggerAwareInterface
{
    public function getLogger(): ?LoggerInterface;
}
