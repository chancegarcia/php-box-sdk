<?php

namespace Box\Tests\Model\Mapper\Fixtures;

use Doctrine\Common\Collections\Collection;

class Group
{
    public string $name;
    /** @var \Box\Tests\Model\Mapper\Fixtures\User[] */
    public Collection $users;
}
