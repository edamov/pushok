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
 * Class Response
 * @package Pushok
 *
 * @see http://bit.ly/communicating-with-apns
 */
class Response implements ApnsResponseInterface
{
    const APNS_SUCCESS = 200;
    const APNS_BAD_REQUEST = 400;
    const APNS_AUTH_PROVIDER_ERROR = 403;
    const APNS_METHOD_NOT_ALLOWED = 405;
    const APNS_NOT_ACTIVE_DEVICE_TOKEN_ = 410;
    const APNS_PAYLOAD_TOO_LARGE = 413;
    const APNS_TOO_MANY_REQUESTS = 429;
    const APNS_SERVER_ERROR = 500;
    const APNS_SERVER_UNAVAILABLE = 503;

    /**
     * Reason phrases by status code.
     *
     * @var array
     */
    private static $reasonPhrases = [
        200 => 'Success.',
        400 => 'Bad request.',
        403 => 'There was an error with the certificate or with the provider authentication token.',
        405 => 'The request used a bad :method value. Only POST requests are supported.',
        410 => 'The device token is no longer active for the topic.',
        413 => 'The notification payload was too large.',
        429 => 'The server received too many requests for the same device token.',
        500 => 'Internal server error.',
        503 => 'The server is shutting down and unavailable.',
    ];

    /**
     * Error reasons by status code.
     *
     * @var array
     */
    private static $errorReasons = [
        400 => [
            'BadCollapseId' => 'The collapse identifier exceeds the maximum allowed size',
            'BadDeviceToken' => 'The specified device token was bad.' .
                ' Verify that the request contains a valid token and that the token matches the environment',
            'BadExpirationDate' => 'The apns-expiration value is bad',
            'BadMessageId' => 'The apns-id value is bad',
            'BadPriority' => 'The apns-priority value is bad',
            'BadTopic' => 'The apns-topic was invalid',
            'DeviceTokenNotForTopic' => 'The device token does not match the specified topic',
            'DuplicateHeaders' => 'One or more headers were repeated',
            'IdleTimeout' => 'Idle time out',
            'MissingDeviceToken' => 'The device token is not specified in the request :path.' .
                ' Verify that the :path header contains the device token',
            'MissingTopic' => 'The apns-topic header of the request was not specified and was required.' .
                ' The apns-topic header is mandatory' .
                ' when the client is connected using a certificate that supports multiple topics',
            'PayloadEmpty' => 'The message payload was empty',
            'TopicDisallowed' => 'Pushing to this topic is not allowed',
        ],
        403 => [
            'BadCertificate' => 'The certificate was bad',
            'BadCertificateEnvironment' => 'The client certificate was for the wrong environment',
            'ExpiredProviderToken' => 'The provider token is stale and a new token should be generated',
            'Forbidden' => 'The specified action is not allowed',
            'InvalidProviderToken' => 'The provider token is not valid or the token signature could not be verified',
            'MissingProviderToken' => 'No provider certificate was used to connect to APNs' .
                ' and Authorization header was missing or no provider token was specified',
        ],
        404 => [
            'BadPath' => 'The request contained a bad :path value'
        ],
        405 => [
            'MethodNotAllowed' => 'The specified :method was not POST'
        ],
        410 => [
            'Unregistered' => 'The device token is inactive for the specified topic.'
        ],
        413 => [
            'PayloadTooLarge' => 'The message payload was too large.' .
                ' See The Remote Notification Payload for details on maximum payload size'
        ],
        429 => [
            'TooManyRequests' => 'Too many requests were made consecutively to the same device token'
        ],
        500 => [
            'InternalServerError' => 'An internal server error occurred'
        ],
        503 => [
            'ServiceUnavailable' => 'The service is unavailable',
            'Shutdown' => 'The server is shutting down',
        ],
    ];

    /**
     * APNs Id.
     *
     * @var string|null
     */
    private $apnsId;

    /**
     * Device token.
     *
     * @var string|null
     */
    private $deviceToken;

    /**
     * Response status code.
     *
     * @var int
     */
    private $statusCode;

    /**
     * Error reason.
     *
     * @var string
     */
    private $errorReason;

    /**
     * Timestamp for a 410 error
     *
     * @var string
     */
    private $error410Timestamp;

    /**
     * Response constructor.
     *
     * @param int $statusCode
     * @param string $headers
     * @param string $body
     * @param string $deviceToken
     */
    public function __construct(int $statusCode, string $headers, string $body, string $deviceToken = null)
    {
        $this->statusCode = $statusCode;
        $this->apnsId = self::fetchApnsId($headers);
        $this->errorReason = self::fetchErrorReason($body);
        $this->error410Timestamp = self::fetch410Timestamp($statusCode, $body);
        $this->deviceToken = $deviceToken;
    }

    /**
     * Fetch APNs Id from response headers.
     *
     * @param string $headers
     * @return string
     */
    private static function fetchApnsId(string $headers): string
    {
        $data = explode("\n", trim($headers));

        foreach ($data as $part) {
            $middle = explode(":", $part);

            if ($middle[0] !== 'apns-id') {
                continue;
            }

            return trim($middle[1]);
        }

        return '';
    }

    /**
     * Fetch error reason from response body.
     *
     * @param string $body
     * @return string
     */
    private static function fetchErrorReason(string $body): string
    {
        return json_decode($body, true)['reason'] ?: '';
    }

    /**
     * Fetch timestamp for a 410 error.
     * https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/sending_notification_requests_to_apns#2947616
     *
     * @param int $statusCode
     * @param string $body
     * @return string
     */
    private static function fetch410Timestamp(int $statusCode, string $body): string
    {
        if ($statusCode === 410) {
            return (string)(json_decode($body, true)['timestamp'] ?: '');
        }
        return '';
    }

    /**
     * Get APNs Id
     *
     * @return string|null
     */
    public function getApnsId()
    {
        return $this->apnsId;
    }

    /**
     * Get device token
     *
     * @return string|null
     */
    public function getDeviceToken()
    {
        return $this->deviceToken;
    }

    /**
     * Get status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get reason phrase.
     *
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return self::$reasonPhrases[$this->statusCode] ?: '';
    }

    /**
     * Get error reason.
     *
     * @return string
     */
    public function getErrorReason(): string
    {
        return $this->errorReason;
    }

    /**
     * Get error description.
     *
     * @return string
     */
    public function getErrorDescription(): string
    {
        if (isset(self::$errorReasons[$this->statusCode][$this->errorReason])) {
            return self::$errorReasons[$this->statusCode][$this->errorReason];
        }

        return '';
    }

    /**
     * Get timestamp for a status 410 error
     *
     * @return string
     */
    public function get410Timestamp(): string
    {
        return $this->error410Timestamp;
    }
}
