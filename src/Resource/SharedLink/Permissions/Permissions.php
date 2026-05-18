<?php

namespace Box\Resource\SharedLink\Permissions;

class Permissions
{
    protected ?bool $canDownload = null;
    protected ?bool $canPreview = null;

    public function getCanDownload(): ?bool
    {
        return $this->canDownload;
    }

    public function setCanDownload(?bool $canDownload = null): void
    {
        $this->canDownload = $canDownload;
    }

    public function getCanPreview(): ?bool
    {
        return $this->canPreview;
    }

    public function setCanPreview(?bool $canPreview = null): void
    {
        $this->canPreview = $canPreview;
    }
}
