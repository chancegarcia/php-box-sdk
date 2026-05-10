<?php

declare(strict_types=1);

namespace Box\Tests\Resource;

use PHPUnit\Framework\TestCase;

class LegacyRemovalTest extends TestCase
{
    /**
     * @dataProvider legacyClassesProvider
     */
    public function testLegacyClassesAreRemoved(string $className): void
    {
        $this->assertFalse(class_exists($className), "Class $className should be removed.");
        $this->assertFalse(interface_exists($className), "Interface $className should be removed.");
    }

    public static function legacyClassesProvider(): array
    {
        return [
            ['Box\Model\BaseModel'],
            ['Box\Model\BaseModelInterface'],
            ['Box\Model\Model'],
            ['Box\Model\ModelInterface'],
            ['Box\Model\BoxModel'],
            ['Box\Model\BoxModelInterface'],
        ];
    }
}
