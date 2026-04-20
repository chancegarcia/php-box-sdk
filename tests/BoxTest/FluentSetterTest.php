<?php

namespace BoxTest;

use Box\Model\Client\Client;
use PHPUnit\Framework\TestCase;

class FluentSetterTest extends TestCase
{
    public function testFluentSettersStillWork()
    {
        $client = new Client();
        
        // Test chaining
        $result = $client->setClientId('foo')->setClientSecret('bar');
        
        $this->assertSame($client, $result);
        $this->assertEquals('foo', $client->getClientId());
        $this->assertEquals('bar', $client->getClientSecret());
    }
}
