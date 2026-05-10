<?php

namespace Box\Tests\Model;

use Box\Mapper\ModelMapper;
use Box\Tests\Model\Fixtures\MappingTestModel;
use PHPUnit\Framework\TestCase;

class MappingTest extends TestCase
{
    public function testToClassVar()
    {
        $this->assertEquals('id', ModelMapper::toClassVar('id'));
        $this->assertEquals('createdAt', ModelMapper::toClassVar('created_at'));
        $this->assertEquals('someVeryLongVariableName', ModelMapper::toClassVar('some_very_long_variable_name'));
    }

    public function testToBoxVar()
    {
        $this->assertEquals('id', ModelMapper::toBoxVar('id'));
        $this->assertEquals('created_at', ModelMapper::toBoxVar('createdAt'));
        $this->assertEquals('some_very_long_variable_name', ModelMapper::toBoxVar('someVeryLongVariableName'));
    }

    public function testMapBoxToClass()
    {
        $data = [
            'id' => '123',
            'name' => 'Test Name',
            'created_at' => '2023-01-01T00:00:00Z',
            'is_true' => true,
            'zero_value' => 0,
            'false_value' => false,
        ];

        $model = new MappingTestModel();
        $model->mapBoxToClass($data);

        $this->assertEquals('123', $model->getId());
        $this->assertEquals('Test Name', $model->getName());
        $this->assertEquals('2023-01-01T00:00:00Z', $model->getCreatedAt());
        $this->assertTrue($model->getIsTrue());
        $this->assertEquals(0, $model->getZeroValue());
        $this->assertFalse($model->getFalseValue());
    }
}
