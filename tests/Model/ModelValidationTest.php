<?php

namespace Box\Tests\Model;

use Box\Exception\BoxException;
use Box\Model\Model;
use PHPUnit\Framework\TestCase;

class ClassWithFailingConstructor
{
    public function __construct()
    {
        throw new \RuntimeException('Constructor should not be called');
    }
}

class ClassImplementingSomething extends Model
{
}

class ModelValidationTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        $this->model = new Model();
    }

    public function testValidateClassDoesNotInstantiate()
    {
        // This should not throw RuntimeException from constructor
        $result = $this->model->validateClass(ClassWithFailingConstructor::class, ClassWithFailingConstructor::class);
        $this->assertTrue($result);
    }

    public function testValidateClassThrowsBoxExceptionForMissingClass()
    {
        $this->expectException(BoxException::class);
        
        try {
            $this->model->validateClass('NonExistentClass', 'SomeInterface');
        } catch (BoxException $e) {
            $this->assertEquals(BoxException::UNKNOWN_CLASS, $e->getBoxCode());
            throw $e;
        }
    }

    public function testValidateClassThrowsBoxExceptionForInvalidSubclass()
    {
        $this->expectException(BoxException::class);

        try {
            $this->model->validateClass(Model::class, \DateTime::class);
        } catch (BoxException $e) {
            $this->assertEquals(BoxException::INVALID_CLASS_TYPE, $e->getBoxCode());
            throw $e;
        }
    }
    
    public function testValidateClassPassesForValidSubclass()
    {
        $result = $this->model->validateClass(ClassImplementingSomething::class, Model::class);
        $this->assertTrue($result);
    }
}
