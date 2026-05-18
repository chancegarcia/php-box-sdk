<?php

namespace Box\Tests\Model\Mapper\Fixtures\V1;

use DateTimeImmutable;

class PlainUserResource
{
    private string|int|null $id = null;
    private ?string $name = null;
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setId(string|int|null $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
