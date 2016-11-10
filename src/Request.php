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
     * Request uri.
     *
     * @var string
     */
    private $uri;

    /**
     * Request body.
     *
     * @var string
     */
    private $body;

    public function __construct(Notification $notification, $isProductionEnv)
    {
        $this->uri = $isProductionEnv ? $this->getProductionUrl($notification) : $this->getSandboxUrl($notification);
        $this->body = $notification->getPayload()->toJson();

        $this->prepareApnsHeaders($notification);
    }

    /**
     * Add request header.
     *
     * @param string $key
     * @param $value
     */
    public function addHeader(string $key, $value)
    {
        $this->headers[$key] = $value;
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

    /**
     * Get request uri.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get request body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
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
        if (!empty($notification->getId())) {
            $this->headers[self::HEADER_APNS_ID] =  $notification->getId();
        }

        if ($notification->getExpirationAt() instanceof \DateTime) {
            $this->headers[self::HEADER_APNS_EXPIRATION] = $notification->getExpirationAt()->getTimestamp();
        }

        if (is_int($notification->getPriority())) {
            $this->headers[self::HEADER_APNS_PRIORITY] =  $notification->getPriority();
        }

        if (!empty($notification->getCollapseId())) {
            $this->headers[self::HEADER_APNS_COLLAPSE_ID ] = $notification->getCollapseId();
        }
    }
}
