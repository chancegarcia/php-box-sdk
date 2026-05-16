<?php

declare(strict_types=1);

namespace Box\Auth\Jwt;

use Box\Exception\BoxException;

interface JwtAssertionGeneratorInterface
{
    /**
     * @throws BoxException
     * @throws \JsonException
     * @throws \Random\RandomException
     */
    public function generate(JwtAuthConfig $config, string $subjectId, string $subjectType): string;
}
