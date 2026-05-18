<?php

declare(strict_types=1);

namespace Box\Tests\Service\Event;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Dto\Event\EventResponse;
use Box\Resource\Event;
use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\Event\UserEventService;
use PHPUnit\Framework\TestCase;

class UserEventServiceTest extends TestCase
{
    private UserEventService $service;

    protected function setUp(): void
    {
        $this->service = new UserEventService();
    }

    /**
     * Characterization test: documents default stream type behavior.
     */
    public function testDefaultStreamTypeIsAll(): void
    {
        $this->assertSame('all', $this->service->getStreamType());
    }

    /**
     * Characterization test: documents valid stream types.
     */
    public function testGetValidStreamTypes(): void
    {
        $expected = ['all', 'changes', 'sync'];
        $this->assertEquals($expected, $this->service->getValidStreamTypes());
    }

    /**
     * Characterization test: documents setting valid stream types.
     */
    public function testSetValidStreamTypes(): void
    {
        $this->service->setStreamType('all');
        $this->assertSame('all', $this->service->getStreamType());

        $this->service->setStreamType('changes');
        $this->assertSame('changes', $this->service->getStreamType());

        $this->service->setStreamType('sync');
        $this->assertSame('sync', $this->service->getStreamType());
    }

    /**
     * Characterization test: documents invalid stream type throwing exception.
     */
    public function testSetInvalidStreamTypeThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('unexpect type (\'invalid\') valid types include: all, changes, sync');

        $this->service->setStreamType('invalid');
    }

    /**
     * Characterization test: documents default limit behavior.
     */
    public function testDefaultLimit(): void
    {
        $this->assertSame(UserEventService::LIMIT_DEFAULT, $this->service->getLimit());
    }

    /**
     * Characterization test: documents setting limit to null resets to default.
     */
    public function testSetLimitNullResetsToDefault(): void
    {
        $this->service->setLimit(50);
        $this->assertSame(50, $this->service->getLimit());

        $this->service->setLimit(null);
        $this->assertSame(UserEventService::LIMIT_DEFAULT, $this->service->getLimit());
    }

    /**
     * Characterization test: documents valid integer limit.
     */
    public function testSetValidLimit(): void
    {
        $this->service->setLimit(500);
        $this->assertSame(500, $this->service->getLimit());
    }

    /**
     * Characterization test: documents limit capped at LIMIT_MAX.
     */
    public function testSetLimitCappedAtMax(): void
    {
        $this->service->setLimit(UserEventService::LIMIT_MAX + 100);
        $this->assertSame(UserEventService::LIMIT_MAX, $this->service->getLimit());
    }

    /**
     * Characterization test: documents invalid limit throwing exception.
     */
    public function testSetInvalidLimitThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('limit must be a valid integer value, (\'invalid\') given');

        /** @noinspection PhpParamsInspection */
        $this->service->setLimit('invalid');
    }

    /**
     * Characterization test: documents numeric string limit is accepted (legacy behavior via isInt).
     */
    public function testSetNumericStringLimit(): void
    {
        /** @noinspection PhpParamsInspection */
        $this->service->setLimit('50');
        $this->assertSame(50, $this->service->getLimit());
    }

    /**
     * v1 test: documents default stream position is "now".
     */
    public function testDefaultStreamPositionIsNow(): void
    {
        $this->assertSame('now', $this->service->getStreamPosition());
    }

    /**
     * Characterization test: documents valid stream positions.
     * Box API: Accepts a numeric string or "now".
     */
    public function testSetValidStreamPosition(): void
    {
        $this->service->setStreamPosition(12345);
        $this->assertSame(12345, $this->service->getStreamPosition());

        $this->service->setStreamPosition('now');
        $this->assertSame('now', $this->service->getStreamPosition());

        $this->service->setStreamPosition('54321');
        $this->assertSame('54321', $this->service->getStreamPosition());
    }

    /**
     * v1 test: documents invalid stream position throwing exception with corrected message.
     */
    public function testSetInvalidStreamPositionThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('stream_position must be a valid integer value or "now", (\'invalid\') given');

        $this->service->setStreamPosition('invalid');
    }

    /**
     * Characterization test: documents URI generation.
     */
    public function testGetEventsUri(): void
    {
        // Default state
        $this->assertSame(
            'https://api.box.com/2.0/events?stream_type=all&stream_position=now&limit=100',
            $this->service->getEventsUri()
        );

        // Changed state
        $this->service->setStreamType('changes');
        $this->service->setStreamPosition(12345);
        $this->service->setLimit(500);

        $this->assertSame(
            'https://api.box.com/2.0/events?stream_type=changes&stream_position=12345&limit=500',
            $this->service->getEventsUri()
        );
    }

    /**
     * v1 test: documents getEvents() behavior returning EventResponse.
     */
    public function testGetEventsReturnsEventResponse(): void
    {
        $eventsData = [
            'entries' => [
                [
                    'type' => 'event',
                    'event_id' => 'f82c3ba03e41f7e8a7608363cc6c0390183c3f83',
                    'event_type' => 'FILE_MARKED_MALICIOUS'
                ]
            ],
            'chunk_size' => 1,
            'next_stream_position' => '1152922976252290886'
        ];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturn($eventsData);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with($this->service->getEventsUri())
            ->willReturn($response);

        $this->service->setConnection($connection);
        $this->service->setToken($this->createMock(TokenInterface::class));

        $result = $this->service->getEvents();

        $this->assertInstanceOf(EventResponse::class, $result);
        $this->assertCount(1, $result->getEntries());
        $this->assertInstanceOf(Event::class, $result->getEntries()->first());
        $this->assertSame('f82c3ba03e41f7e8a7608363cc6c0390183c3f83', $result->getEntries()->first()->getEventId());
        $this->assertSame(1, $result->getChunkSize());
        $this->assertSame('1152922976252290886', $result->getNextStreamPosition());
    }
}
