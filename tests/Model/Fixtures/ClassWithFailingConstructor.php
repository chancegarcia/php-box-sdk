<?php

namespace Box\Tests\Model\Fixtures;

class ClassWithFailingConstructor
{
    public function __construct()
    {
        throw new \RuntimeException('Constructor should not be called');
    }
}
