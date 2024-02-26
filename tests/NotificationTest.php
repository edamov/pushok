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

use DateTime;
use PHPUnit\Framework\TestCase;
use Pushok\Notification;
use Pushok\Payload;

class NotificationTest extends TestCase
{
    public function testGetDeviceToken()
    {
        $message = new Notification(Payload::create(), 'deviceTokenString');

        $this->assertSame('deviceTokenString', $message->getDeviceToken());
    }

    public function testGetPayload()
    {
        $payload = Payload::create();

        $message = new Notification($payload, 'deviceTokenString');

        $this->assertSame($payload, $message->getPayload());
    }

    public function testId()
    {
        $message = new Notification(Payload::create(), 'deviceTokenString');

        $id = 'this is a string';
        $message->setId($id);

        $this->assertSame($id, $message->getId());
    }

    public function testExpirationAt()
    {
        $message = new Notification(Payload::create(), 'deviceTokenString');

        $expire = (new DateTime())->modify('+1 day');
        $expected = $expire->getTimestamp();

        $message->setExpirationAt($expire);

        // Change object to see unwanted behaviour with object references
        $expire->modify('+2 days');

        $this->assertSame($expected, $message->getExpirationAt()->getTimestamp());
    }

    public function testPriority()
    {
        $message = new Notification(Payload::create(), 'deviceTokenString');

        $message->setHighPriority();
        $this->assertSame(Notification::PRIORITY_HIGH, $message->getPriority());

        $message->setLowPriority();
        $this->assertSame(Notification::PRIORITY_LOW, $message->getPriority());
    }

    public function testCollapseId()
    {
        $message = new Notification(Payload::create(), 'deviceTokenString');

        $id = 'this is a string';
        $message->setCollapseId($id);

        $this->assertSame($id, $message->getCollapseId());
    }
}
