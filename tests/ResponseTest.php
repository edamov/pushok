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
use Pushok\Response;

class ResponseTest extends TestCase
{
    public function testGetStatusCode()
    {
        $response = new Response(200, 'headers', 'body');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetApnsId()
    {
        $response = new Response(200, 'apns-id: 123', 'body');

        $this->assertEquals('123', $response->getApnsId());
    }

    public function testGetReasonPhrase()
    {
        $response = new Response(200, 'headers', 'body');

        $this->assertEquals('Success.', $response->getReasonPhrase());
    }

    public function testGetErrorReason()
    {
        $response = new Response(400, 'headers', '{"reason": "BadCollapseId"}');

        $this->assertEquals('BadCollapseId', $response->getErrorReason());
    }

    public function testGetErrorDescription()
    {
        $response = new Response(400, 'headers', '{"reason": "BadCollapseId"}');

        $this->assertEquals('The collapse identifier exceeds the maximum allowed size', $response->getErrorDescription());
    }
}
