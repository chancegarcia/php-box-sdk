<?php

declare(strict_types=1);

namespace Box\Tests\Mapper;

use Box\Dto\Event\EventResponse;
use Box\Resource\Event;
use Box\Exception\BoxException;
use Box\Mapper\EventResponseMapper;
use PHPUnit\Framework\TestCase;
use stdClass;

class EventResponseMapperTest extends TestCase
{
    private EventResponseMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new EventResponseMapper();
    }

    public function testMapArrayPayload(): void
    {
        $data = [
            'entries' => [
                ['type' => 'event', 'event_id' => '123'],
                ['type' => 'event', 'event_id' => '456'],
            ],
            'chunk_size' => 2,
            'next_stream_position' => '789'
        ];

        $response = $this->mapper->map($data);

        $this->assertInstanceOf(EventResponse::class, $response);
        $this->assertCount(2, $response->getEntries());
        $this->assertSame(2, $response->getChunkSize());
        $this->assertSame('789', $response->getNextStreamPosition());
        $this->assertInstanceOf(Event::class, $response->getEntries()->first());
        $this->assertSame('123', $response->getEntries()->first()->getEventId());
    }

    public function testMapStdClassPayload(): void
    {
        $data = new stdClass();
        $data->entries = [['type' => 'event', 'event_id' => '123']];
        $data->chunk_size = 1;
        $data->next_stream_position = '999';

        $response = $this->mapper->map($data);

        $this->assertInstanceOf(EventResponse::class, $response);
        $this->assertCount(1, $response->getEntries());
        $this->assertSame(1, $response->getChunkSize());
        $this->assertSame('999', $response->getNextStreamPosition());
    }

    public function testMapEmptyEntries(): void
    {
        $data = [
            'entries' => [],
            'chunk_size' => 0,
            'next_stream_position' => '0'
        ];

        $response = $this->mapper->map($data);

        $this->assertCount(0, $response->getEntries());
        $this->assertSame('0', $response->getNextStreamPosition());
    }

    public function testMapMissingEntries(): void
    {
        $data = [
            'chunk_size' => 0,
            'next_stream_position' => '0'
        ];

        $response = $this->mapper->map($data);

        $this->assertCount(0, $response->getEntries());
        $this->assertSame('0', $response->getNextStreamPosition());
    }

    public function testMapLargeNumericStreamPositionAsString(): void
    {
        $data = [
            'entries' => [],
            'chunk_size' => 0,
            'next_stream_position' => 1152922976252290886
        ];

        $response = $this->mapper->map($data);

        $this->assertSame('1152922976252290886', $response->getNextStreamPosition());
    }

    public function testMapMissingNextStreamPositionThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Events response is missing required "next_stream_position" field.');

        $data = [
            'entries' => [],
            'chunk_size' => 0
        ];

        $this->mapper->map($data);
    }
}
