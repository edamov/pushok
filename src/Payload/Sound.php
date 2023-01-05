<?php

/*
 * This file is part of the Pushok package.
 *
 * (c) Arthur Edamov <edamov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pushok\Payload;

/**
 * Class Sound
 *
 * @package Pushok\Payload
 *
 * @see http://bit.ly/payload-key-reference
 */
class Sound implements \JsonSerializable
{
    const SOUND_CRITICAL_KEY = 'critical';
    const SOUND_NAME_KEY = 'name';
    const SOUND_VOLUME_KEY = 'volume';

    /**
     * Whether the sound should be played as a critical notification or not
     *
     * @var integer
     */
    private $critical;

    /**
     * The sound file name.
     *
     * @var string
     */
    private $name;

    /**
     * The sound volume.
     *
     * @var float
     */
    private $volume;

    protected function __construct()
    {
    }

    public static function create()
    {
        return new self();
    }

    /**
     * Set Sound critical.
     *
     * @param int $value
     * @return Sound
     */
    public function setCritical(int $value): Sound
    {
        $this->critical = $value;

        return $this;
    }

    /**
     * Get Sound critical.
     *
     * @return string
     */
    public function getCritical()
    {
        return $this->critical;
    }

    /**
     * Set Sound name.
     *
     * @param string $value
     * @return Sound
     */
    public function setName(string $value): Sound
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Get Sound name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Sound volume.
     *
     * @param float $value
     * @return Sound
     */
    public function setVolume(float $value): Sound
    {
        $this->volume = $value;

        return $this;
    }

    /**
     * Get Sound volume.
     *
     * @return float
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * Convert Sound to JSON.
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
    public function jsonSerialize(): array
    {
        $sound = [];

        if (is_integer($this->critical)) {
            $sound[self::SOUND_CRITICAL_KEY] = $this->critical;
        }

        if (is_string($this->name)) {
            $sound[self::SOUND_NAME_KEY] = $this->name;
        }

        if (is_float($this->volume)) {
            $sound[self::SOUND_VOLUME_KEY] = $this->volume;
        }

        return $sound;
    }
}
