<?php

namespace Box\Tests\Model\Mapper;

use Box\Mapper\Hydrator;
use Box\Tests\Model\Mapper\Fixtures\V1\PlainCollectionHolder;
use Box\Tests\Model\Mapper\Fixtures\V1\PlainEnterpriseResource;
use Box\Tests\Model\Mapper\Fixtures\V1\PlainUserResource;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class HydratorV1ReadinessTest extends TestCase
{
    private Hydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator();
    }

    public function testHydrateFromClassString(): void
    {
        $data = ['id' => '123', 'name' => 'John Doe'];
        $result = $this->hydrator->hydrate(PlainUserResource::class, $data);

        $this->assertInstanceOf(PlainUserResource::class, $result);
        $this->assertEquals('123', $result->getId());
        $this->assertEquals('John Doe', $result->getName());
    }

    public function testHydrateExistingInstance(): void
    {
        $user = new PlainUserResource();
        $data = ['id' => '456'];
        $result = $this->hydrator->hydrate($user, $data);

        $this->assertSame($user, $result);
        $this->assertEquals('456', $user->getId());
    }

    public function testPublicPropertyHydration(): void
    {
        $data = ['id' => 'ent_1', 'name' => 'Box Inc'];
        $result = $this->hydrator->hydrate(PlainEnterpriseResource::class, $data);

        $this->assertEquals('ent_1', $result->id);
        $this->assertEquals('Box Inc', $result->name);
    }

    public function testSnakeToCamelMapping(): void
    {
        $data = ['created_at' => '2024-01-01T00:00:00Z'];
        $result = $this->hydrator->hydrate(PlainUserResource::class, $data);

        $this->assertInstanceOf(DateTimeImmutable::class, $result->getCreatedAt());
        $this->assertEquals('2024-01-01T00:00:00Z', $result->getCreatedAt()->format('Y-m-d\TH:i:s\Z'));
    }

    public function testNullableScalarValues(): void
    {
        $data = ['name' => null];
        $result = $this->hydrator->hydrate(PlainUserResource::class, $data);
        $this->assertNull($result->getName());
    }

    public function testBoxIdsAsStrings(): void
    {
        $data = ['id' => '9876543210987654321'];
        $result = $this->hydrator->hydrate(PlainUserResource::class, $data);
        $this->assertIsString($result->getId());
        $this->assertEquals('9876543210987654321', $result->getId());
    }

    public function testIgnoreUnknownFields(): void
    {
        $data = ['id' => '123', 'unknown_field' => 'value'];
        $result = $this->hydrator->hydrate(PlainUserResource::class, $data);
        $this->assertEquals('123', $result->getId());
        // Should not throw exception
    }

    public function testCollectionHydrationWithInference(): void
    {
        $data = [
            'users' => [
                ['id' => '1', 'name' => 'User 1'],
                ['id' => '2', 'name' => 'User 2'],
            ]
        ];

        $result = $this->hydrator->hydrate(PlainCollectionHolder::class, $data);

        $this->assertInstanceOf(PlainCollectionHolder::class, $result);
        $this->assertCount(2, $result->getUsers());
        $this->assertInstanceOf(PlainUserResource::class, $result->getUsers()[0]);
        $this->assertEquals('User 1', $result->getUsers()[0]->getName());
    }
}
