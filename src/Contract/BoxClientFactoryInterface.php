<?php

namespace Box\Contract;

use Box\Client;

interface BoxClientFactoryInterface
{
    public function createClient(): Client;
}
