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
     * Message payload
     *
     * @var Payload
     */
    private $payload;

    /**
     * Token of device
     *
     * @var string
     */
    private $deviceToken;

    /**
     * Message constructor.
     *
     * @param Payload $payload
     * @param string $deviceToken
     */
    public function __construct(Payload $payload, string $deviceToken)
    {
        $this->payload = $payload;
        $this->deviceToken = $deviceToken;
    }

    /**
     * Get device token.
     *
     * @return string
     */
    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }

    /**
     * Get payload.
     *
     * @return Payload
     */
    public function getPayload(): Payload
    {
        return $this->payload;
    }
}
