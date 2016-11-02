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
 * Class Request
 * @package Pushok
 *
 * @see http://bit.ly/communicating-with-apns
 */
class Request
{
    const APNS_DEVELOPMENT_SERVER = 'https://api.development.push.apple.com';
    const APNS_PRODUCTION_SERVER = 'https://api.push.apple.com';
    const APNS_PORT = 443;
    const APNS_PATH_SCHEMA = '/3/device/{token}';

    private $options = [];

    public function __construct(Message $message, $isProductionEnv)
    {
        if ($isProductionEnv) {
            $url = $this->getProductionUrl($message);
        } else {
            $url = $this->getSandboxUrl($message);
        }

        $this->options = [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_URL => $url,
            CURLOPT_PORT => self::APNS_PORT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $message->getPayload()->toJson(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => true,
        ];
    }

    public function getOptions()
    {
        return $this->options;
    }

    private function getProductionUrl(Message $message)
    {
        return self::APNS_PRODUCTION_SERVER . $this->getUrlPath($message);
    }

    private function getSandboxUrl(Message $message)
    {
        return self::APNS_DEVELOPMENT_SERVER . $this->getUrlPath($message);
    }

    private function getUrlPath(Message $message)
    {
        return str_replace("{token}", $message->getDeviceToken(), self::APNS_PATH_SCHEMA);
    }
}