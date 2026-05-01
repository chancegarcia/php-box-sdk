<?php

namespace Box\Tests\Model\Fixtures;

use Box\Model\Model;

class MappingTestModel extends Model
{
    protected $id;
    protected $name;
    protected $createdAt;
    protected $isTrue;
    protected $zeroValue;
    protected $falseValue;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getIsTrue()
    {
        return $this->isTrue;
    }
    public function setIsTrue($isTrue): void
    {
        $this->isTrue = $isTrue;
    }

    public function getZeroValue()
    {
        return $this->zeroValue;
    }
    public function setZeroValue($zeroValue): void
    {
        $this->zeroValue = $zeroValue;
    }

    public function getFalseValue()
    {
        return $this->falseValue;
    }
    public function setFalseValue($falseValue): void
    {
        $this->falseValue = $falseValue;
    }
}
