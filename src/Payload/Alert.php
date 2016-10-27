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

class Alert
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string|null
     */
    private $titleLocKey;

    /**
     * @var string[]|null
     */
    private $titleLocArgs;

    /**
     * @var string|null
     */
    private $actionLocKey;

    /**
     * @var string
     */
    private $locKey;

    /**
     * @var string[]
     */
    private $locArgs;

    /**
     * @var string
     */
    private $launchImage;

    /**
     * @var bool
     */
    private $mutableContent;

    public static function createFromArray(array $array)
    {

    }

    public static function createFromJson(string $json)
    {

    }

    public function setTitle(string $value)
    {
        $this->title = $value;
    }

    public function setBody(string $value)
    {
        $this->body = $value;
    }

    public function setTitleLocKey(string $value = null)
    {
        $this->titleLocKey = $value;
    }

    public function setTitleLocArgs(array $value = null)
    {
        $this->titleLocArgs = $value;
    }

    public function setActionLocKey(string $value = null)
    {
        $this->actionLocKey = $value;
    }

    public function setLocKey(string $value)
    {
        $this->locKey = $value;
    }

    public function setLocArgs(array $value)
    {
        $this->locArgs = $value;
    }

    public function setLaunchImage(string $value)
    {
        $this->launchImage = $value;
    }

    /**
     * Set the mutable-content key for Notification Service Extensions on iOS10
     * @see https://developer.apple.com/reference/usernotifications/unnotificationserviceextension
     *
     * @param bool $value
     */
    public function setMutableContent(bool $value)
    {
        $this->mutableContent = $value;
    }
}