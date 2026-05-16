<?php

namespace Box\Storage\Token\Pdo;

use Box\Storage\Token\TokenStorageInterface as BaseInterface;
use PDO;

interface TokenStorageInterface extends BaseInterface
{
    public function getDsn(): ?string;

    public function setDsn(?string $dsn = null): void;

    public function getUsername(): ?string;

    public function setUsername(?string $username = null): void;

    public function getPassword(): ?string;

    public function setPassword(?string $password = null): void;

    public function getOptions(): array;

    public function setOptions(?array $options = null): void;

    public function getPdo(): ?PDO;

    public function setPdo(?PDO $pdo = null): void;

    public function getTokenTableName(): string;

    public function setTokenTableName(?string $tokenTableName = null): void;

    public function getTokenMap(): array;

    public function setTokenMap(?array $tokenMap = null): void;
}
