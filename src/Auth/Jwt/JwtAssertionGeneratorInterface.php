<?php

declare(strict_types=1);

namespace Box\Auth\Jwt;

interface JwtAssertionGeneratorInterface
{
    public function generate(JwtAuthConfig $config, string $subjectId, string $subjectType): string;
}
