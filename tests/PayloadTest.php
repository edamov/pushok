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
use Pushok\InvalidPayloadException;
use Pushok\Payload;
use Pushok\Payload\Alert;
use Pushok\Payload\Sound;

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
        $sound = Sound::create();
        $payload = Payload::create()->setSound($sound);

        $this->assertSame($sound, $payload->getSound());
    }

    public function testSetInterruptionLevel()
    {
        $interruptionLevel = 'active';
        $payload = Payload::create()->setInterruptionLevel($interruptionLevel);

        $this->assertSame($interruptionLevel, $payload->getInterruptionLevel());
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

    public function testSetCustomValueToRootKey()
    {
        $this->expectException(InvalidPayloadException::class);
        $this->expectExceptionMessage("Key aps is reserved and can't be used for custom property.");

        Payload::create()->setCustomValue('aps', 'value');
    }

    public function testGetCustomValueOfNotExistingKey()
    {
        $this->expectException(InvalidPayloadException::class);
        $this->expectExceptionMessage("Custom value with key 'notExistingKey' doesn't exist.");

        Payload::create()
            ->setCustomValue('something', 'value')
            ->getCustomValue('notExistingKey', 'value');
    }

    public function testSetPushType()
    {
        $payload = Payload::create()->setPushType('pushType');

        $this->assertEquals('pushType', $payload->getPushType());
    }

    public function testConvertToJSon()
    {

        $alert = Alert::create()->setTitle('title');
        $sound = Sound::create()->setName('soundName')->setCritical(1)->setVolume(1.0);
        $payload = Payload::create()
            ->setAlert($alert)
            ->setBadge(1)
            ->setSound($sound)
            ->setInterruptionLevel('time-sensitive')
            ->setCategory('category')
            ->setThreadId('tread-id')
            ->setContentAvailability(true)
            ->setMutableContent(true)
            ->setCustomValue('key', 'value');

        $this->assertJsonStringEqualsJsonString(
            '{"aps": {"alert": {"title": "title"}, "badge": 1, "sound": {"critical": 1, "name": "soundName", "volume": 1.0}, "interruption-level": "time-sensitive", "category": "category", ' .
            ' "thread-id": "tread-id", "mutable-content": 1, "content-available": 1}, "key": "value"}',
            $payload->toJson()
        );
    }

    public function testSetCustomArrayType()
    {
        $alert = Alert::create()->setTitle('title');
        $payload = Payload::create()
            ->setAlert($alert)
            ->setCustomValue('array', array(1,2,3));

        $this->assertEquals(gettype(json_decode($payload->toJson())->array), 'array');
    }

    public function testJsonSizeException()
    {
        $this->expectException(InvalidPayloadException::class);

        $alert = Alert::create()->setTitle(
            str_repeat('title that is going to be waaaaaaay to big and is going to throw an error to avoid having a request failing', 40)
        );
        $sound = Sound::create()->setName('soundName')->setCritical(1)->setVolume(1.0);
        $payload = Payload::create()
            ->setAlert($alert)
            ->setBadge(1)
            ->setSound($sound)
            ->setCategory('category')
            ->setThreadId('tread-id')
            ->setContentAvailability(true)
            ->setMutableContent(true)
            ->setCustomValue('key', 'value');

         $payload->toJson();
    }
}
