<?php

namespace Box\Http\Response\Header;

interface ResponseHeaderInterface
{
    public function getStatusLine(): ?StatusLineInterface;

    public function setStatusLine(?StatusLineInterface $statusLine = null): ResponseHeaderInterface;

    public function getHeaderLines(): array;

    /**
     * @param array|null $headerLines
     */
    public function setHeaderLines(?array $headerLines = null): ResponseHeaderInterface;
}
