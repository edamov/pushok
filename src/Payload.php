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

use Pushok\Payload\Alert;

/**
 * Class Payload
 * @package Pushok
 *
 * @see http://bit.ly/payload-key-reference
 */
class Payload implements \JsonSerializable
{
    const PAYLOAD_ROOT_KEY = 'aps';
    const PAYLOAD_ALERT_KEY = 'alert';
    const PAYLOAD_BADGE_KEY = 'badge';
    const PAYLOAD_SOUND_KEY = 'sound';
    const PAYLOAD_CONTENT_AVAILABLE_KEY = 'content-available';
    const PAYLOAD_CATEGORY_KEY = 'category';
    const PAYLOAD_THREAD_ID_KEY = 'thread-id';

    const PAYLOAD_HTTP2_REGULAR_NOTIFICATION_MAXIMUM_SIZE = 4096;
    const PAYLOAD_HTTP2_VOIP_NOTIFICATION_MAXIMUM_SIZE = 5120;
    const PAYLOAD_BINARY_REGULAR_NOTIFICATION_MAXIMUM_SIZE = 2048;

    /**
     * The notification settings for your app on the user’s device determine whether an alert or banner is displayed.
     *
     * @var Alert
     */
    private $alert;

    /**
     * The number to display as the badge of the app icon.
     * If this property is absent, the badge is not changed.
     *
     * @var int
     */
    private $badge;

    /**
     * The name of a sound file in the app bundle or in the Library/Sounds folder of the app’s data container.
     *
     * @var string
     */
    private $sound;

    /**
     * Include this key with a value of true to configure a silent notification.
     *
     * @var bool
     */
    private $contentAvailable;

    /**
     * Provide this key with a string value that represents the notification’s type.
     *
     * @var string
     */
    private $category;

    /**
     * Provide this key with a string value that represents the app-specific identifier for grouping notifications.
     *
     * @var string
     */
    private $threadId;

    /**
     * Payload custom values.
     *
     * @var array
     */
    private $customValues;

    protected function __construct()
    {
    }

    /**
     * @return Payload
     */
    public static function create(): Payload
    {
        return new self();
    }

    /**
     * Set Alert.
     *
     * @param Alert $alert
     * @return Payload
     */
    public function setAlert(Alert $alert): Payload
    {
        $this->alert = $alert;

        return $this;
    }

    /**
     * Get Alert.
     *
     * @return Alert|null
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * Set badge.
     *
     * @param int $value
     * @return Payload
     */
    public function setBadge(int $value): Payload
    {
        $this->badge = $value;

        return $this;
    }

    /**
     * Get badge.
     *
     * @return int|null
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * Set sound.
     *
     * @param string $value
     * @return Payload
     */
    public function setSound(string $value): Payload
    {
        $this->sound = $value;

        return $this;
    }

    /**
     * Get sound.
     *
     * @return string|null
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * Set content availability.
     *
     * @param bool $value
     * @return Payload
     */
    public function setContentAvailability(bool $value): Payload
    {
        $this->contentAvailable = $value;

        return $this;
    }

    /**
     * Get content availability.
     *
     * @return bool|null
     */
    public function isContentAvailable()
    {
        return $this->contentAvailable;
    }

    /**
     * Set category.
     *
     * @param string $value
     * @return Payload
     */
    public function setCategory(string $value): Payload
    {
        $this->category = $value;

        return $this;
    }

    /**
     * Get category.
     *
     * @return string|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set thread-id.
     *
     * @param string $value
     * @return Payload
     */
    public function setThreadId(string $value): Payload
    {
        $this->threadId = $value;

        return $this;
    }

    /**
     * Get thread-id.
     *
     * @return string|null
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * Set custom value for Payload.
     *
     * @param string $key
     * @param mixed $value
     * @return Payload
     * @throws InvalidPayloadException
     */
    public function setCustomValue(string $key, $value): Payload
    {
        if ($key === self::PAYLOAD_ROOT_KEY) {
            throw InvalidPayloadException::reservedKey();
        }

        $this->customValues[$key] = $value;

        return $this;
    }

    /**
     * Get custom value.
     *
     * @param $key
     * @return mixed
     * @throws InvalidPayloadException
     */
    public function getCustomValue($key)
    {
        if (!array_key_exists($key, $this->customValues)) {
            throw InvalidPayloadException::notExistingCustomValue($key);
        }

        return $this->customValues[$key];
    }

    /**
     * Convert Payload to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     * @link   http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        $payload = self::getDefaultPayloadStructure();

        if ($this->alert instanceof Alert) {
            $payload[self::PAYLOAD_ROOT_KEY][self::PAYLOAD_ALERT_KEY] = $this->alert;
        }

        if (is_int($this->badge)) {
            $payload[self::PAYLOAD_ROOT_KEY][self::PAYLOAD_BADGE_KEY] = $this->badge;
        }

        if (is_string($this->sound)) {
            $payload[self::PAYLOAD_ROOT_KEY][self::PAYLOAD_SOUND_KEY] = $this->sound;
        }

        if (is_bool($this->contentAvailable)) {
            $payload[self::PAYLOAD_ROOT_KEY][self::PAYLOAD_CONTENT_AVAILABLE_KEY] = (int)$this->contentAvailable;
        }

        if (is_string($this->category)) {
            $payload[self::PAYLOAD_ROOT_KEY][self::PAYLOAD_CATEGORY_KEY] = $this->category;
        }

        if (is_string($this->threadId)) {
            $payload[self::PAYLOAD_ROOT_KEY][self::PAYLOAD_THREAD_ID_KEY] = $this->threadId;
        }

        if (count($this->customValues)) {
            $payload = array_merge($payload, $this->customValues);
        }

        return $payload;
    }

    private static function getDefaultPayloadStructure()
    {
        return [self::PAYLOAD_ROOT_KEY => []];
    }
}
