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
use Pushok\Message;

class ClientTest extends TestCase
{
    public function testAmountOfAddedMessages()
    {
        $authProvider = $this->createMock(Token::class);
        $message = $this->createMock(Message::class);

        $client = new Client($authProvider, $production = false);
        $client->addMessage($message);

        $this->assertEquals(1, count($client->getMessages()));
    }
}
