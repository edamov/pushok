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

class Payload
{
    /**
     * Root payload key
     */
    const PAYLOAD_ROOT_KEY = 'aps';

    const PAYLOAD_HTTP2_REGULAR_NOTIFICATION_MAXIMUM_SIZE = 4096;
    const PAYLOAD_HTTP2_VOIP_NOTIFICATION_MAXIMUM_SIZE = 5120;
    const PAYLOAD_BINARY_REGULAR_NOTIFICATION_MAXIMUM_SIZE = 2048;

    /**
     * @var Alert|string
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

    private $isContentAvailable;

    private $threadId;

    /**
     * @var array
     */
    private $customValues;

    /**
     * @param Alert|string $alert
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;
    }

    /**
     * Set badge value.
     *
     * @param integer $value
     */
    public function setBadge(integer $value)
    {
        $this->badge = $value;
    }

    /**
     * Set the name of a sound file.
     *
     * @param string $value
     */
    public function setSound(string $value)
    {
        $this->sound = $value;
    }

    public function isContentAvailable(bool $value)
    {
        $this->isContentAvailable = $value;
    }

    public function setThreadId($value)
    {
        $this->threadId = $value;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setCustomValue(string $key, $value)
    {
        $this->customValues[$key] = $value;
    }

    public function toJson()
    {
        return '{"aps":{"alert":"hello!","sound":"default"}}';
    }
}
