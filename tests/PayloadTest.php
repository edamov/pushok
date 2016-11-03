<?php

namespace Pushok\Tests;

use Pushok\Payload;
use Pushok\Payload\Alert;

class PayloadTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAlert()
    {
        $alert = new Alert();
        $payload = Payload::create()->setAlert($alert);

        $this->assertSame($alert, $payload->getAlert());
    }

    public function testSetBadge()
    {
        $payload = Payload::create()->setBadge(3);

        $this->assertSame(3, $payload->getBadge());
    }

    public function testSetSound()
    {
        $payload = Payload::create()->setSound('soundString');

        $this->assertSame('soundString', $payload->getSound());
    }
}
