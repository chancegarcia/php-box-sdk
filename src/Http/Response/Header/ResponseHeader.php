<?php

namespace Box\Http\Response\Header;

use Box\Exception\BoxException;
use Box\Http\Response\ResponseParser;
use Box\Http\Response\Header\StatusLine;
use Box\Http\Response\Header\StatusLineInterface;

class ResponseHeader implements ResponseHeaderInterface
{
    /**
     * @var StatusLineInterface
     */
    protected $statusLine;

    /**
     * @var array
     */
    protected $headerLines = [];

    /**
     * @throws BoxException
     */
    public function __construct(string $header = '', string $statusLineClass = StatusLine::class)
    {
        $parsedHeader = ResponseParser::parseHeader($header);
        $rawStatusLine = array_shift($parsedHeader);
        if ($statusLineClass !== StatusLine::class && !is_subclass_of($statusLineClass, StatusLineInterface::class)) {
            $msg = "status line class must be an instance of " . StatusLineInterface::class . " ("
                . $statusLineClass
                . ") given.";
            throw new BoxException($msg, BoxException::INVALID_CLASS_TYPE);
        }

        $statusLineObj = new $statusLineClass($rawStatusLine);

        $this->setStatusLine($statusLineObj);
        $this->setHeaderLines($parsedHeader);
    }

    public function getStatusLine(): ?StatusLineInterface
    {
        return $this->statusLine;
    }

    public function setStatusLine(?StatusLineInterface $statusLine = null): ResponseHeaderInterface
    {
        $this->statusLine = $statusLine;

        return $this;
    }

    public function getHeaderLines(): array
    {
        return $this->headerLines;
    }

    public function setHeaderLines(?array $headerLines = null): ResponseHeaderInterface
    {
        $this->headerLines = $headerLines;

        return $this;
    }

    public static function parseHeader(string $headers = '', bool $replace = true): array
    {
        $finalHeaders = [];
        $headerLines = explode(PHP_EOL, $headers);
        foreach ($headerLines as $headerLineKey => $headerLineValue) {
            // based on protocols found on https://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html
            // first line is Status Line
            if (0 === $headerLineKey) {
                $finalHeaders[] = $headerLineValue;
            } else {
                // rest of the lines are headers
                [$key, $value] = array_map("trim", explode(":", $headerLineValue));
                if (true === $replace || !array_key_exists($key, $finalHeaders)) {
                    $finalHeaders[$key] = $value;
                } else {
                    $finalHeaders[$key] = array_merge((array) $finalHeaders[$key], (array) $value);
                }
            }
        }

        return $finalHeaders;
    }
}
