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

/**
 * Class Notification
 * @package Pushok
 */
class Notification
{
    const PRIORITY_HIGH = 10;
    const PRIORITY_LOW = 5;

    /**
     * Notification payload.
     *
     * @var Payload
     */
    private $payload;

    /**
     * Token of device.
     *
     * @var string
     */
    private $deviceToken;

    /**
     * A canonical UUID that identifies the notification.
     *
     * @var string
     */
    private $id;

    /**
     * This value identifies the date when the notification is no longer valid and can be discarded.
     *
     * @var \DateTime
     */
    private $expirationAt;

    /**
     * The priority of the notification.
     *
     * @var int
     */
    private $priority;

    /**
     * Id for the coalescing of similar notifications.
     *
     * @var string
     */
    private $collapseId;

    /**
     * Notification constructor.
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

    /**
     * Get notification id.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set notification id.
     *
     * @param string $id
     * @return Notification
     */
    public function setId(string $id): Notification
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get expiration DateTime.
     *
     * @return \DateTime
     */
    public function getExpirationAt()
    {
        return $this->expirationAt;
    }

    /**
     * Set expiration DateTime.
     *
     * @param \DateTime $expirationAt
     * @return Notification
     */
    public function setExpirationAt(\DateTime $expirationAt): Notification
    {
        $this->expirationAt = $expirationAt;

        return $this;
    }

    /**
     * Get notification priority.
     *
     * @return int|null
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Set high priority.
     *
     * @return Notification
     */
    public function setHighPriority(): Notification
    {
        $this->priority = self::PRIORITY_HIGH;

        return $this;
    }

    /**
     * Set low priority.
     *
     * @return Notification
     */
    public function setLowPriority(): Notification
    {
        $this->priority = self::PRIORITY_LOW;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCollapseId()
    {
        return $this->collapseId;
    }

    /**
     * @param string $collapseId
     * @return Notification
     */
    public function setCollapseId(string $collapseId): Notification
    {
        $this->collapseId = $collapseId;

        return $this;
    }
}
