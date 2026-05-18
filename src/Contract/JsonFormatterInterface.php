<?php

declare(strict_types=1);

namespace Box\Contract;

interface JsonFormatterInterface
{
    public function format(array $data): string;
}
