<?php

namespace Box\Exception;

use Box\Dto\TokenStorageContext;
use Box\Storage\Token\TokenStorageInterface;

class TokenStorageException extends \Exception
{
    protected ?TokenStorageInterface $tokenStorage = null;
    protected ?TokenStorageContext $tokenStorageContext = null;

    public function getTokenStorage(): ?TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function setTokenStorage(?TokenStorageInterface $tokenStorage = null): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getTokenStorageContext(): ?TokenStorageContext
    {
        return $this->tokenStorageContext;
    }

    public function setTokenStorageContext(?TokenStorageContext $tokenStorageContext = null): void
    {
        $this->tokenStorageContext = $tokenStorageContext;
    }
}
