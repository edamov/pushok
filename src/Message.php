<?php

/*
 * This file is part of the Pushok package.
 *
 * (c) Arthur Edamov <edamov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pushok;

class Message
{
    /**
     * @var Payload
     */
    private $payload;

    /**
     * @var string
     */
    private $deviceToken;

    public function __construct(Payload $payload, string $deviceToken)
    {
        $this->payload = $payload;
        $this->deviceToken = $deviceToken;
    }

    public function getDeviceToken()
    {
        return $this->deviceToken;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
