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
 * Class InvalidPayloadException
 * @package Pushok
 */
class InvalidPayloadException extends \Exception
{
    public static function reservedKey()
    {
        return new static("Key " . Payload::PAYLOAD_ROOT_KEY . " is reserved and can't be used for custom property.");
    }

    public static function notExistingCustomValue($key)
    {
        return new static("Custom value with key '$key' doesn't exist.");
    }
}
