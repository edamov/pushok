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
use Pushok\AuthProvider\Token;
use Pushok\Client;
use Pushok\Notification;
use ReflectionClass;

class ClientTest extends TestCase
{
    public function testAmountOfAddedMessages()
    {
        $notification = $this->createMock(Notification::class);
        $notification2 = $this->createMock(Notification::class);
        $notification3 = $this->createMock(Notification::class);
        $notification4 = $this->createMock(Notification::class);

        $client = $this->getClient();

        $client->addNotification($notification);
        $client->addNotifications([$notification, $notification2, $notification3]);
        $client->addNotification($notification4);

        $this->assertEquals(4, count($client->getNotifications()));
    }

    private function getClient(): Client
    {
        $authProvider = $this->createMock(Token::class);

        $client = new Client($authProvider, $production = false);

        return $client;
    }

    public function testPrepareHandle()
    {
        $client = $this->getClient();

        $method = (new ReflectionClass($client))->getMethod('prepareHandle');
        $method->setAccessible(true);


        $notification = $this->createMock(Notification::class);

        $ch = $method->invoke($client, $notification);

        $this->assertIsResource($ch);
        $this->assertTrue(curl_errno($ch) === 0);
    }


}
