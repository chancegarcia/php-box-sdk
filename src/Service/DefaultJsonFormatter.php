<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Contract\JsonFormatterInterface;

class DefaultJsonFormatter implements JsonFormatterInterface
{
    public function format(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }
}
