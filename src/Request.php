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
    const HEADER_APNS_PUSH_TYPE = 'apns-push-type';

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

    /**
     * Curl options.
     *
     * @var array
     */
    private $options;

    public function __construct(Notification $notification, $isProductionEnv)
    {
        $this->uri = $isProductionEnv ? $this->getProductionUrl($notification) : $this->getSandboxUrl($notification);
        $this->body = $notification->getPayload()->toJson();

        if (!defined('CURL_HTTP_VERSION_2')) {
            define('CURL_HTTP_VERSION_2', 3);
        }

        $this->options = [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2,
            CURLOPT_URL => $this->uri,
            CURLOPT_PORT => self::APNS_PORT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $notification->getPayload()->toJson(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HEADER => true,
        ];

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
     * @param array $headers
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
     * Add request option.
     *
     * @param string $key
     * @param $value
     */
    public function addOption(string $key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * Add request options.
     *
     * @param array $options
     */
    public function addOptions(array $options)
    {
        $this->options = array_replace($this->options, $options);
    }

    /**
     * Get request options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get decorated request headers.
     *
     * @return array
     */
    public function getDecoratedHeaders(): array
    {
        $decoratedHeaders = [];
        foreach ($this->headers as $name => $value) {
            $decoratedHeaders[] = $name . ': ' . $value;
        }
        return $decoratedHeaders;
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

    /**
     * Get Url for APNs production server.
     *
     * @param Notification $notification
     * @return string
     */
    private function getProductionUrl(Notification $notification)
    {
        return self::APNS_PRODUCTION_SERVER . $this->getUrlPath($notification);
    }

    /**
     * Get Url for APNs sandbox server.
     *
     * @param Notification $notification
     * @return string
     */
    private function getSandboxUrl(Notification $notification)
    {
        return self::APNS_DEVELOPMENT_SERVER . $this->getUrlPath($notification);
    }

    /**
     * Get Url path.
     *
     * @param Notification $notification
     * @return mixed
     */
    private function getUrlPath(Notification $notification)
    {
        return str_replace("{token}", $notification->getDeviceToken(), self::APNS_PATH_SCHEMA);
    }

    /**
     * Prepare APNs headers before sending request.
     *
     * @param Notification $notification
     */
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
        } elseif ($notification->getPayload()->isContentAvailable()) {
            $this->headers[self::HEADER_APNS_PRIORITY] = Notification::PRIORITY_LOW;
        }

        if (!empty($notification->getCollapseId())) {
            $this->headers[self::HEADER_APNS_COLLAPSE_ID ] = $notification->getCollapseId();
        }
        // if the push type was set when the payload was created then it will set that as a push type,
        // otherwise we would do our best in order to guess what push type is.
        if (!empty($notification->getPayload()->getPushType())) {
            $this->headers[self::HEADER_APNS_PUSH_TYPE] = $notification->getPayload()->getPushType();
        } else if ($notification->getPayload()->isContentAvailable()) {
            $this->headers[self::HEADER_APNS_PUSH_TYPE] = 'background';
        } else {
            $this->headers[self::HEADER_APNS_PUSH_TYPE] = 'alert';
        }
    }
}
