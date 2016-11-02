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
class Payload
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
     * @var integer
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

    protected function __constructor()
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
     * Set alert.
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
     * Set badge.
     *
     * @param integer $value
     * @return Payload
     */
    public function setBadge(integer $value): Payload
    {
        $this->badge = $value;

        return $this;
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
     * Set content availability.
     *
     * @param bool $value
     * @return Payload
     */
    public function isContentAvailable(bool $value): Payload
    {
        $this->contentAvailable = $value;

        return $this;
    }

    /**
     * Set category.
     *
     * @param string $value
     * @return Payload
     */
    public function setCategory(string $value): Payload
    {
        $this->threadId = $value;

        return $this;
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
     * Set custom value for Payload.
     *
     * @param string $key
     * @param $value
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
     * Convert Payload to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        $payload = $this->transform();

        return json_encode($payload);
    }

    /**
     * Transform Payload object to array.
     *
     * @return array
     */
    private function transform(): array
    {
        $payload = [
            self::PAYLOAD_ROOT_KEY => [
                self::PAYLOAD_ALERT_KEY => $this->alert,
                self::PAYLOAD_BADGE_KEY => $this->badge,
                self::PAYLOAD_SOUND_KEY => $this->sound,
                self::PAYLOAD_CONTENT_AVAILABLE_KEY => $this->contentAvailable,
                self::PAYLOAD_CATEGORY_KEY => $this->category,
                self::PAYLOAD_THREAD_ID_KEY => $this->threadId,
            ]
        ];

        $payload = array_merge($payload, $this->customValues);

        return $payload;
    }
}
