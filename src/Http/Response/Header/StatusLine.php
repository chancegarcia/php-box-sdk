<?php

namespace Box\Http\Response\Header;

use Box\Http\Response\ResponseParser;

class StatusLine implements StatusLineInterface
{
    protected $httpVersion = "HTTP/1.1";
    protected $httpVersionPrefix = "HTTP/";
    protected $httpVersionNumber = "1.1";
    protected $statusCode = 200;
    protected $reasonPhrase = "OK";

    public function __construct(string $statusLine = '')
    {
        if (!empty($statusLine)) {
            [$httpVersion, $statusCode, $reasonPhrase] = ResponseParser::parseHeaderStatusLine($statusLine, false);

            [$httpVersionPrefix, $httpVersionNumber] = explode("/", $httpVersion);
            $code = filter_var($statusCode, FILTER_VALIDATE_INT);

            $this->httpVersion = $httpVersion;
            $this->httpVersionPrefix = $httpVersionPrefix . "/";
            $this->httpVersionNumber = $httpVersionNumber;
            $this->statusCode = $code;
            $this->reasonPhrase = $reasonPhrase;
        }
    }

    public function getHttpVersionPrefix(): string
    {
        return $this->httpVersionPrefix;
    }

    public function setHttpVersionPrefix(?string $httpVersionPrefix = null): void
    {
        $this->httpVersionPrefix = $httpVersionPrefix;
    }

    public function getHttpVersionNumber(): string
    {
        return $this->httpVersionNumber;
    }

    public function setHttpVersionNumber(?string $httpVersionNumber = null): void
    {
        $this->httpVersionNumber = $httpVersionNumber;
    }

    public function getHttpVersion(): string
    {
        return $this->httpVersion;
    }

    public function setHttpVersion(?string $httpVersion = null): void
    {
        $this->httpVersion = $httpVersion;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(?int $statusCode = null): void
    {
        $this->statusCode = $statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function setReasonPhrase(?string $reasonPhrase = null): void
    {
        $this->reasonPhrase = $reasonPhrase;
    }
}
