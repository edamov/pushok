<?php

namespace Pushok\Tests\Payload;

use Pushok\Payload\Alert;

class AlertTest extends \PHPUnit_Framework_TestCase
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

    public function testLocKey()
    {
        $alert = Alert::create()->setLocKey('loc-key');

        $this->assertEquals('loc-key', $alert->getLocKey());
    }

    public function testLocArgs()
    {
        $alert = Alert::create()->setLocArgs(['loc-arg1', 'loc-arg2']);

        $this->assertEquals(['loc-arg1', 'loc-arg2'], $alert->getLocArgs());
    }

    public function testLaunchImage()
    {
        $alert = Alert::create()->setLaunchImage('launch-image');

        $this->assertEquals('launch-image', $alert->getLaunchImage());
    }

    public function testSetMutableContent()
    {
        $alert = Alert::create()->setMutableContent(true);

        $this->assertEquals(true, $alert->hasMutableContent());
    }

    public function testAlertTransformer()
    {
        $alert = Alert::create()
            ->setTitle('title')
            ->setBody('body')
            ->setTitleLocKey('title-loc-key')
            ->setTitleLocArgs(['title-loc-arg'])
            ->setActionLocKey('action-loc-key')
            ->setLocKey('loc-key')
            ->setLocArgs(['loc-arg'])
            ->setLaunchImage('launch-image')
            ->setMutableContent(true);

        $this->assertEquals([
            'title' => 'title',
            'body' => 'body',
            'title-loc-key' => 'title-loc-key',
            'title-loc-args' => ['title-loc-arg'],
            'action-loc-key' => 'action-loc-key',
            'loc-key' => 'loc-key',
            'loc-args' => ['loc-arg'],
            'launch-image' => 'launch-image',
            'mutable-content' => 1,
        ], $alert->transform());
    }
}
