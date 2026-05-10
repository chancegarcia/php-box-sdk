<?php

declare(strict_types=1);

namespace Box\Tests\Dto\Event;

use Box\Dto\Event\EventResponse;
use Box\Resource\Event;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class EventResponseTest extends TestCase
{
    public function testImmutabilityViaDefensiveCopy(): void
    {
        $entries = new ArrayCollection([new Event()]);
        $response = new EventResponse($entries, 1, '123');

        $returnedEntries = $response->getEntries();
        $this->assertCount(1, $returnedEntries);
        $this->assertNotSame($entries, $returnedEntries, 'Should not return the internal collection instance');

        $returnedEntries->clear();
        $this->assertCount(0, $returnedEntries);
        $this->assertCount(1, $response->getEntries(), 'Internal collection should remain unchanged');
    }

    public function testGetters(): void
    {
        $entries = new ArrayCollection([new Event()]);
        $response = new EventResponse($entries, 1, '123');

        $this->assertSame(1, $response->getChunkSize());
        $this->assertSame('123', $response->getNextStreamPosition());
        $this->assertCount(1, $response->getEntries());
    }
}
