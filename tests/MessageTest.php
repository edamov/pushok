<?php

namespace Pushok\Tests;

use Pushok\Message;
use Pushok\Payload;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDeviceToken()
    {
        $message = new Message(Payload::create(), 'deviceTokenString');

        $this->assertEquals('deviceTokenString', $message->getDeviceToken());
    }

    public function testGetPayload()
    {
        $payload = Payload::create();

        $message = new Message($payload, 'deviceTokenString');

        $this->assertSame($payload, $message->getPayload());
    }
}
