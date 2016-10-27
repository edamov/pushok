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

final class Request
{
    const APNS_DEVELOPMENT_SERVER = 'https://api.development.push.apple.com';
    const APNS_PRODUCTION_SERVER = 'https://api.push.apple.com';
    const APNS_PORT = 443;
    const APNS_PATH_SCHEMA = '/3/device/{token}';

    public function __construct($curlHandle, Message $message, $isProductionEnv)
    {
        $this->curlHandle = $curlHandle;

        if ($isProductionEnv) {
            $url = $this->getProductionUrl($message);
        } else {
            $url = $this->getSandboxUrl($message);
        }

        curl_setopt_array($curlHandle, array(
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_URL => $url,
            CURLOPT_PORT => self::APNS_PORT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $message->getPayload()->toJson(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => 1,
        ));

//        $response = curl_exec($curlHandle);
//        if ($response === FALSE) {
//            throw new \Exception("Curl failed: " .  curl_error($curlHandle));
//        }
//        print_r($response);
//
//        // get response
//        $status = curl_getinfo($curlHandle);
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

    public function send()
    {
        return curl_exec($this->curlHandle);
    }
}