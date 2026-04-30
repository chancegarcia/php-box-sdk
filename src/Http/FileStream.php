<?php

namespace Box\Http;

class FileStream
{
    private mixed $resource;
    private string $filename;
    private ?string $mimeType;

    public function __construct(mixed $resource, string $filename, ?string $mimeType = null)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('First argument must be a valid resource.');
        }
        $this->resource = $resource;
        $this->filename = $filename;
        $this->mimeType = $mimeType;
    }

    public function getResource(): mixed
    {
        return $this->resource;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Create from a string of content
     */
    public static function fromString(string $content, string $filename, ?string $mimeType = null): self
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);
        rewind($resource);

        return new self($resource, $filename, $mimeType);
    }

    /**
     * Create from a local file path
     */
    public static function fromPath(string $path, ?string $filename = null, ?string $mimeType = null): self
    {
        $resource = fopen($path, 'r');
        if (null === $filename) {
            $filename = basename($path);
        }

        return new self($resource, $filename, $mimeType);
    }
}
