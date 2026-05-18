<?php

namespace Box\Tests\Model\Mapper\Fixtures\V1;

use Doctrine\Common\Collections\Collection;

class PlainCollectionHolder
{
    /** @var PlainUserResource[] */
    private ?Collection $users = null;

    /**
     * @return Collection|PlainUserResource[]|null
     */
    public function getUsers(): ?Collection
    {
        return $this->users;
    }

    /**
     * @param Collection|PlainUserResource[]|null $users
     */
    public function setUsers(?Collection $users): void
    {
        $this->users = $users;
    }
}
