<?php

namespace Box\Http\Response\Header;

interface StatusLineInterface
{
    public function getHttpVersion(): string;

    public function setHttpVersion(?string $httpVersion = null): void;

    public function getStatusCode(): int;

    public function setStatusCode(?int $statusCode = null): void;

    public function getReasonPhrase(): string;

    public function setReasonPhrase(?string $reasonPhrase = null): void;

    public function getHttpVersionPrefix(): string;

    public function setHttpVersionPrefix(?string $httpVersionPrefix = null): void;

    public function getHttpVersionNumber(): string;

    public function setHttpVersionNumber(?string $httpVersionNumber = null): void;
}
