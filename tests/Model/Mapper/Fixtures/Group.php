<?php

namespace Box\Tests\Model\Mapper\Fixtures;

use Doctrine\Common\Collections\Collection;
use Box\Tests\Model\Mapper\Fixtures\User;

class Group
{
    public string $name;
    /** @var User[] */
    public Collection $users;
}
