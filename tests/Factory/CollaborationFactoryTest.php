<?php

namespace Box\Tests\Factory;

use Box\Factory\CollaborationFactory;
use Box\Resource\Collaboration;
use PHPUnit\Framework\TestCase;

class CollaborationFactoryTest extends TestCase
{
    public function testCreateCollaborationReturnsEmptyResourceWhenOptionsIsNull(): void
    {
        $factory = new CollaborationFactory();
        $collaboration = $factory->createCollaboration(null);

        $this->assertInstanceOf(Collaboration::class, $collaboration);
        $this->assertNull($collaboration->getId());
    }

    public function testCreateCollaborationHydratesWhenOptionsIsProvided(): void
    {
        $factory = new CollaborationFactory();
        $options = [
            'id' => 'abc',
            'role' => 'editor',
            'status' => 'accepted'
        ];
        $collaboration = $factory->createCollaboration($options);

        $this->assertInstanceOf(Collaboration::class, $collaboration);
        $this->assertEquals('abc', $collaboration->getId());
        $this->assertEquals('editor', $collaboration->getRole());
        $this->assertEquals('accepted', $collaboration->getStatus());
    }
}
