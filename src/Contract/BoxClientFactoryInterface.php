<?php

namespace Box\Contract;

use Box\Client;
use Psr\Log\LoggerInterface;

interface BoxClientFactoryInterface
{
    public function createClient(): Client;

    public function setLogger(LoggerInterface $logger): void;
}
