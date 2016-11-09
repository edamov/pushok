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

    const HEADER_APNS_ID = 'apns-id';
    const HEADER_APNS_EXPIRATION = 'apns-expiration';
    const HEADER_APNS_PRIORITY = 'apns-priority';
    const HEADER_APNS_TOPIC = 'apns-topic';
    const HEADER_APNS_COLLAPSE_ID = 'apns-collapse-id';

    /**
     * Request headers.
     *
     * @var array
     */
    private $headers = [];

    /**
     * Curl options.
     *
     * @var array
     */
    private $options = [];

    public function __construct(Notification $notification, $isProductionEnv)
    {
        $url = $isProductionEnv ? $this->getProductionUrl($notification) : $this->getSandboxUrl($notification);

        if (!defined('CURL_HTTP_VERSION_2')) {
            define('CURL_HTTP_VERSION_2', 3);
        }

        $this->options = [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2,
            CURLOPT_URL => $url,
            CURLOPT_PORT => self::APNS_PORT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $notification->getPayload()->toJson(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => true,
        ];

        $this->prepareApnsHeaders($notification);
    }

    /**
     * Add curl options.
     *
     * @param int $key
     * @param $value
     * @return Request
     */
    public function addOption(int $key, $value): Request
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Add curl options.
     *
     * @param array $options
     * @return Request
     */
    public function addOptions(array $options): Request
    {
        $this->headers = array_merge($this->options, $options);

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add request header.
     *
     * @param string $header
     */
    public function addHeader(string $header)
    {
        $this->headers[] = $header;
    }

    /**
     * Add request headers.
     *
     * @param Notification[] $headers
     */
    public function addHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Get request headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    private function getProductionUrl(Notification $notification)
    {
        return self::APNS_PRODUCTION_SERVER . $this->getUrlPath($notification);
    }

    private function getSandboxUrl(Notification $notification)
    {
        return self::APNS_DEVELOPMENT_SERVER . $this->getUrlPath($notification);
    }

    private function getUrlPath(Notification $notification)
    {
        return str_replace("{token}", $notification->getDeviceToken(), self::APNS_PATH_SCHEMA);
    }

    private function prepareApnsHeaders(Notification $notification)
    {
        if ($notification->getId()) {
            $this->headers[] = self::HEADER_APNS_ID . ': ' . $notification->getId();
        }

        if ($notification->getExpirationAt() instanceof \DateTime) {
            $this->headers[] = self::HEADER_APNS_EXPIRATION . ': ' . $notification->getExpirationAt()->getTimestamp();
        }

        if ($notification->getPriority()) {
            $this->headers[] = self::HEADER_APNS_PRIORITY . ': ' . $notification->getPriority();
        }

        if ($notification->getCollapseId()) {
            $this->headers[] = self::HEADER_APNS_COLLAPSE_ID . ': ' . $notification->getCollapseId();
        }
    }
}
