<?php

/*
 * This file is part of the Pushok package.
 *
 * (c) Arthur Edamov <edamov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pushok\Tests;

use PHPUnit\Framework\TestCase;
use Pushok\Notification;
use Pushok\Payload;

class MessageTest extends TestCase
{
    public function testGetDeviceToken()
    {
        $message = new Notification(Payload::create(), 'deviceTokenString');

        $this->assertEquals('deviceTokenString', $message->getDeviceToken());
    }

    public function testGetPayload()
    {
        $payload = Payload::create();

        $message = new Notification($payload, 'deviceTokenString');

        $this->assertSame($payload, $message->getPayload());
    }
}
