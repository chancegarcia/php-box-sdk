<?php

namespace Box\Resource;

use Box\Exception\GroupException;
use DateTimeInterface;

class Group
{
    protected string $type = 'group';
    protected string|int|null $id = null;
    protected ?string $name = null;
    protected DateTimeInterface|string|null $createdAt = null;
    protected DateTimeInterface|string|null $modifiedAt = null;

    public function setId(string|int|null $id = null): void
    {
        $this->id = $id;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setCreatedAt(DateTimeInterface|string|null $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): DateTimeInterface|string|null
    {
        return $this->createdAt;
    }

    public function setModifiedAt(DateTimeInterface|string|null $modifiedAt = null): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    public function getModifiedAt(): DateTimeInterface|string|null
    {
        return $this->modifiedAt;
    }

    /**
     * @throws GroupException
     */
    public function setName(?string $name = null): void
    {
        if ($name !== null && strlen($name) > 255) {
            throw new GroupException(
                "Box only supports group names of 255 characters or less. " .
                "Names that will not be supported are the name “none” or a null name.",
                GroupException::INVALID_NAME
            );
        }

        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
