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
use Pushok\Payload;
use Pushok\Payload\Alert;

class PayloadTest extends TestCase
{
    public function testSetAlert()
    {
        $alert = Alert::create();
        $payload = Payload::create()->setAlert($alert);

        $this->assertSame($alert, $payload->getAlert());
    }

    public function testSetBadge()
    {
        $payload = Payload::create()->setBadge(3);

        $this->assertEquals(3, $payload->getBadge());
    }

    public function testSetSound()
    {
        $payload = Payload::create()->setSound('soundString');

        $this->assertEquals('soundString', $payload->getSound());
    }

    public function testSetCategory()
    {
        $payload = Payload::create()->setCategory('categoryString');

        $this->assertEquals('categoryString', $payload->getCategory());
    }

    public function testSetThreadId()
    {
        $payload = Payload::create()->setThreadId('thread-id');

        $this->assertEquals('thread-id', $payload->getThreadId());
    }

    public function testSetContentAvailability()
    {
        $payload = Payload::create()->setContentAvailability(true);

        $this->assertTrue($payload->isContentAvailable());
    }

    public function testSetMutableContent()
    {
        $payload = Payload::create()->setMutableContent(true);

        $this->assertEquals(true, $payload->hasMutableContent());
    }

    public function testSetCustomValue()
    {
        $payload = Payload::create()->setCustomValue('key', 'value');

        $this->assertEquals('value', $payload->getCustomValue('key'));
    }

    public function testConvertToJSon()
    {
        $alert = Alert::create()->setTitle('title');

        $payload = Payload::create()
            ->setAlert($alert)
            ->setBadge(1)
            ->setSound('sound')
            ->setCategory('category')
            ->setThreadId('tread-id')
            ->setContentAvailability(true)
            ->setMutableContent(true)
            ->setCustomValue('key', 'value');

        $this->assertJsonStringEqualsJsonString(
            '{"aps": {"alert": {"title": "title"}, "badge": 1, "sound": "sound", "category": "category", ' .
            ' "thread-id": "tread-id", "mutable-content": 1, "content-available": 1}, "key": "value"}',
            $payload->toJson()
        );
    }
}
