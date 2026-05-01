<?php

declare(strict_types=1);

namespace Box\Contract;

interface JsonFormatterInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function format(array $data): string;
}
