<?php

namespace Box\Tests\Model;

use Box\Model\Model;
use Box\Tests\Model\Fixtures\MappingTestModel;
use PHPUnit\Framework\TestCase;

class MappingTest extends TestCase
{
    public function testToClassVar()
    {
        $model = new MappingTestModel();
        $this->assertEquals('id', $model->toClassVar('id'));
        $this->assertEquals('createdAt', $model->toClassVar('created_at'));
        $this->assertEquals('someVeryLongVariableName', $model->toClassVar('some_very_long_variable_name'));
    }

    public function testToBoxVar()
    {
        $model = new MappingTestModel();
        $this->assertEquals('id', $model->toBoxVar('id'));
        $this->assertEquals('created_at', $model->toBoxVar('createdAt'));
        $this->assertEquals('some_very_long_variable_name', $model->toBoxVar('someVeryLongVariableName'));
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

    public function testRemoveEmpty()
    {
        $model = new MappingTestModel();
        $data = [
            'keep' => 'value',
            'remove_null' => null,
            'remove_empty_string' => '',
            'remove_whitespace' => '   ',
            'keep_zero' => 0,
            'keep_zero_string' => '0',
            'keep_false' => false,
            'nested' => [
                'remove' => '',
                'keep' => 1
            ]
        ];

        $result = $model->removeEmpty($data);

        $this->assertArrayHasKey('keep', $result);
        $this->assertArrayNotHasKey('remove_null', $result);
        $this->assertArrayNotHasKey('remove_empty_string', $result);
        $this->assertArrayNotHasKey('remove_whitespace', $result);

        // Current behavior audit: check if it keeps 0, "0", false
        // The issue description says removeEmpty uses empty(), which removes 0, "0", false.
        $this->assertArrayNotHasKey('keep_zero', $result, 'Current removeEmpty drops 0');
        $this->assertArrayNotHasKey('keep_zero_string', $result, 'Current removeEmpty drops "0"');
        $this->assertArrayNotHasKey('keep_false', $result, 'Current removeEmpty drops false');

        $this->assertEquals(['keep' => 1], $result['nested']);
    }

    public function testToBoxArray()
    {
        $model = new MappingTestModel();
        $model->setId('123');
        $model->setName('Test');
        $model->setZeroValue(0);

        $result = $model->toBoxArray();

        $this->assertEquals('123', $result['id']);
        $this->assertEquals('Test', $result['name']);
        // With v0.11.0 improvements, 0 should be preserved
        $this->assertArrayHasKey('zero_value', $result);
        $this->assertEquals(0, $result['zero_value']);
    }
}
