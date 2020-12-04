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

use Countable;
use Pushok\Payload\Alert;
use Pushok\Payload\Sound;

// Polyfill for PHP 7.2
if (!function_exists('is_countable')) {
    function is_countable($var)
    {
        return (is_array($var) || $var instanceof Countable);
    }
}

/**
 * Class Payload
 *
 * @package Pushok
 *
 * @see     http://bit.ly/payload-key-reference
 */
class Payload implements \JsonSerializable
{
    const PAYLOAD_ROOT_KEY = 'aps';
    const PAYLOAD_ALERT_KEY = 'alert';
    const PAYLOAD_BADGE_KEY = 'badge';
    const PAYLOAD_SOUND_KEY = 'sound';
    const PAYLOAD_CONTENT_AVAILABLE_KEY = 'content-available';
    const PAYLOAD_MUTABLE_CONTENT_KEY = 'mutable-content';
    const PAYLOAD_CATEGORY_KEY = 'category';
    const PAYLOAD_THREAD_ID_KEY = 'thread-id';
    const PAYLOAD_URL_ARGS_KEY = 'url-args';

    const PAYLOAD_HTTP2_REGULAR_NOTIFICATION_MAXIMUM_SIZE = 4096;
    const PAYLOAD_HTTP2_VOIP_NOTIFICATION_MAXIMUM_SIZE = 5120;
    const PAYLOAD_BINARY_REGULAR_NOTIFICATION_MAXIMUM_SIZE = 2048;


    /**
     * The notification settings for your app on the user’s device determine whether an alert or banner is displayed.
     *
     * @var Alert|string
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
     * @var Sound|string
     */
    private $sound;

    /**
     * Include this key with a value of true to configure a silent notification.
     *
     * @var bool
     */
    private $contentAvailable;

    /**
     * Include this key with a value of true to configure a mutable content notification.
     *
     * @var bool
     */
    private $mutableContent;

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
     * Provide this key with an array value that represents the url-args for Safari notifications.
     *
     * @var string
     */
    private $urlArgs;

    /**
     * Payload custom values.
     *
     * @var array
     */
    private $customValues;

    /**
     * Push notification type
     *
     * https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/sending_notification_requests_to_apns#2947607
     *
     * @var string
     */
    private $pushType;

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
     * Get Alert.
     *
     * @return Alert|null
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * Set Alert.
     *
     * @param Alert|string $alert
     *
     * @return Payload
     */
    public function setAlert($alert): Payload
    {
        if ($alert instanceof Alert || is_string($alert)) {
            $this->alert = $alert;
        }

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
     * Set badge.
     *
     * @param int $value
     *
     * @return Payload
     */
    public function setBadge(int $value): Payload
    {
        $this->badge = $value;

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
     * Set sound.
     *
     * @param Sound|string $sound
     *
     * @return Payload
     */
    public function setSound($sound): Payload
    {
        if ($sound instanceof Sound || is_string($sound)) {
            $this->sound = $sound;
        }

        return $this;
    }

    /**
     * Set content availability.
     *
     * @param bool $value
     *
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
     * Set the mutable-content key for Notification Service Extensions on iOS10.
     *
     * @see http://bit.ly/mutable-content
     *
     * @param bool $value
     *
     * @return Payload
     */
    public function setMutableContent(bool $value): Payload
    {
        $this->mutableContent = $value;

        return $this;
    }

    /**
     * Is content mutable.
     *
     * @return bool|null
     */
    public function hasMutableContent()
    {
        return $this->mutableContent;
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
     * Set category.
     *
     * @param string $value
     *
     * @return Payload
     */
    public function setCategory(string $value): Payload
    {
        $this->category = $value;

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
     * Set thread-id.
     *
     * @param string $value
     *
     * @return Payload
     */
    public function setThreadId(string $value): Payload
    {
        $this->threadId = $value;

        return $this;
    }

    /**
     * Get url-args.
     *
     * @return array|null
     */
    public function getUrlArgs()
    {
        return $this->urlArgs;
    }

    /**
     * Set url-args.
     *
     * @param array $value
     *
     * @return Payload
     */
    public function setUrlArgs(array $value): Payload
    {
        $this->urlArgs = $value;

        return $this;
    }

    /**
     * Set custom value for Payload.
     *
     * @param string $key
     * @param mixed $value
     *
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
     *
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
     * Set push type for Payload.
     *
     * @param string $pushType
     * @return Payload
     */
    public function setPushType($pushType) {
        $this->pushType = $pushType;

        return $this;
    }

    /**
     * Get push type for Payload.
     *
     * @return string
     */
    public function getPushType() {
        return $this->pushType;
    }

    /**
     * Convert Payload to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        $str = json_encode($this, JSON_UNESCAPED_UNICODE);

        $this->checkPayloadSize($str);

        return $str;
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

        if ($this->alert instanceof Alert || is_string($this->alert)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_ALERT_KEY} = $this->alert;
        }

        if (is_int($this->badge)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_BADGE_KEY} = $this->badge;
        }

        if ($this->sound instanceof Sound || is_string($this->sound)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_SOUND_KEY} = $this->sound;
        }

        if (is_bool($this->contentAvailable)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_CONTENT_AVAILABLE_KEY} = (int)$this->contentAvailable;
        }

        if (is_bool($this->mutableContent)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_MUTABLE_CONTENT_KEY} = (int)$this->mutableContent;
        }

        if (is_string($this->category)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_CATEGORY_KEY} = $this->category;
        }

        if (is_string($this->threadId)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_THREAD_ID_KEY} = $this->threadId;
        }

        if (is_array($this->urlArgs)) {
            $payload[self::PAYLOAD_ROOT_KEY]->{self::PAYLOAD_URL_ARGS_KEY} = $this->urlArgs;
        }

        if (is_countable($this->customValues) && count($this->customValues)) {
            $payload = array_merge($payload, $this->customValues);
        }

        return $payload;
    }

    /**
     * Get default payload structure.
     *
     * @return array
     */
    private static function getDefaultPayloadStructure()
    {
        return [self::PAYLOAD_ROOT_KEY => new \stdClass];
    }

    /**
     * @param $jsonPayload
     * @return void
     * @throws InvalidPayloadException
     */
    private function checkPayloadSize($jsonPayload)
    {
        $strLength = strlen($jsonPayload);
        if ('voip' === $this->getPushType()) {
            if ($strLength > self::PAYLOAD_HTTP2_VOIP_NOTIFICATION_MAXIMUM_SIZE) {
                throw new InvalidPayloadException('Voip Payload size limit exceeded');
            }
        } elseif ($strLength > self::PAYLOAD_HTTP2_REGULAR_NOTIFICATION_MAXIMUM_SIZE) {
            throw new InvalidPayloadException('Payload size limit exceeded');
        }
    }
}
