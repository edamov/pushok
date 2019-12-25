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
use Pushok\Payload\Alert;

class AlertTest extends TestCase
{
    public function testSetTitle()
    {
        $alert = Alert::create()->setTitle('title');

        $this->assertEquals('title', $alert->getTitle());
    }

    public function testSetBody()
    {
        $alert = Alert::create()->setBody('body');

        $this->assertEquals('body', $alert->getBody());
    }

    public function testSetTitleLocKey()
    {
        $alert = Alert::create()->setTitleLocKey('title-loc-key');

        $this->assertEquals('title-loc-key', $alert->getTitleLocKey());
    }

    public function testSetTitleLocArgs()
    {
        $alert = Alert::create()->setTitleLocArgs(['title1', 'title2']);

        $this->assertEquals(['title1', 'title2'], $alert->getTitleLocArgs());
    }

    public function testSetActionLocKey()
    {
        $alert = Alert::create()->setActionLocKey('action-loc-key');

        $this->assertEquals('action-loc-key', $alert->getActionLocKey());
    }

    public function testSetLocKey()
    {
        $alert = Alert::create()->setLocKey('loc-key');

        $this->assertEquals('loc-key', $alert->getLocKey());
    }

    public function testSetLocArgs()
    {
        $alert = Alert::create()->setLocArgs(['loc-arg1', 'loc-arg2']);

        $this->assertEquals(['loc-arg1', 'loc-arg2'], $alert->getLocArgs());
    }

    public function testSetLaunchImage()
    {
        $alert = Alert::create()->setLaunchImage('launch-image');

        $this->assertEquals('launch-image', $alert->getLaunchImage());
    }

    public function testAlertConvertingToJson()
    {
        $alert = Alert::create()
            ->setTitle('title')
            ->setBody('body')
            ->setTitleLocKey('title-loc-key')
            ->setTitleLocArgs(['loc-arg'])
            ->setActionLocKey('action-loc-key')
            ->setLocKey('loc-key')
            ->setLocArgs(['loc-arg'])
            ->setLaunchImage('launch-image');

        $this->assertJsonStringEqualsJsonString(
            '{"title":"title","body":"body","title-loc-key":"title-loc-key","title-loc-args":["loc-arg"],' .
            '"action-loc-key":"action-loc-key","loc-key":"loc-key","loc-args":["loc-arg"],' .
            '"launch-image":"launch-image"}',
            $alert->toJson()
        );
    }
}
