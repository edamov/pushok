<?php

/*
 * This file is part of the Pushok package.
 *
 * (c) Arthur Edamov <edamov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pushok\Tests\Payload;

use PHPUnit\Framework\TestCase;
use Pushok\Payload\Sound;

class SoundTest extends TestCase
{
    public function testSetCritical()
    {
        $sound = Sound::create()->setCritical(1);

        $this->assertEquals(1, $sound->getCritical());
    }

    public function testSetName()
    {
        $sound = Sound::create()->setName('soundName');

        $this->assertEquals('soundName', $sound->getName());
    }

    public function testSetVolume()
    {
        $sound = Sound::create()->setVolume(1.0);

        $this->assertEquals(1.0, $sound->getVolume());
    }

    public function testSoundConvertingToJson()
    {
        $sound = Sound::create()
            ->setCritical(1)
            ->setName('soundName')
            ->setVolume(1.0);

        $this->assertJsonStringEqualsJsonString(
            '{"critical":1,"name":"soundName","volume":1.0}',
            $sound->toJson()
        );
    }
}
