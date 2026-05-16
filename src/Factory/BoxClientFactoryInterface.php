<?php

namespace Box\Factory;

use Box\Auth\Jwt\JwtAuthConfig;
use Box\Client;
use Psr\Log\LoggerInterface;

interface BoxClientFactoryInterface
{
    public function createOAuth2Client(): Client;

    public function createJwtClient(JwtAuthConfig $config): Client;

    public function createClientForCurrentMode(): Client;

    public function setLogger(LoggerInterface $logger): void;
}
