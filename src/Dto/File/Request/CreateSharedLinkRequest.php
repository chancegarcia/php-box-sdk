<?php

namespace Box\Dto\File\Request;

use DateTimeInterface;

class CreateSharedLinkRequest
{
    private ?string $access = null;
    private ?DateTimeInterface $unsharedAt = null;
    private ?bool $canDownload = null;
    private ?bool $canPreview = null;

    public function __construct()
    {
    }

    public function withAccess(string $access): self
    {
        $clone = clone $this;
        $clone->access = $access;
        return $clone;
    }

    public function withUnsharedAt(DateTimeInterface $unsharedAt): self
    {
        $clone = clone $this;
        $clone->unsharedAt = $unsharedAt;
        return $clone;
    }

    public function withCanDownload(bool $canDownload): self
    {
        $clone = clone $this;
        $clone->canDownload = $canDownload;
        return $clone;
    }

    public function withCanPreview(bool $canPreview): self
    {
        $clone = clone $this;
        $clone->canPreview = $canPreview;
        return $clone;
    }

    public function toArray(): array
    {
        $data = [];
        if ($this->access !== null) {
            $data['access'] = $this->access;
        }
        if ($this->unsharedAt !== null) {
            $data['unshared_at'] = $this->unsharedAt->format(DateTimeInterface::RFC3339);
        }
        if ($this->canDownload !== null) {
            $data['permissions']['can_download'] = $this->canDownload;
        }
        if ($this->canPreview !== null) {
            $data['permissions']['can_preview'] = $this->canPreview;
        }
        return $data;
    }
}
