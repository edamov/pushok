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
 * Class Client
 * @package Pushok
 */
class Client
{
    /**
     * Array of notifications.
     *
     * @var Notification[]
     */
    private $notifications = [];

    /**
     * Authentication provider.
     *
     * @var AuthProviderInterface
     */
    private $authProvider;

    /**
     * Production or sandbox environment.
     *
     * @var bool
     */
    private $isProductionEnv;

    /**
     * Number of concurrent requests to multiplex in the same connection.
     *
     * @var int
     */
    private $nbConcurrentRequests = 20;

    /**
     * Number of maximum concurrent connections established to the APNS servers.
     *
     * @var int
     */
    private $maxConcurrentConnections = 1;

    /**
     * Flag to know if we should automatically close connections to the APNS servers or keep them alive.
     *
     * @var bool
     */
    private $autoCloseConnections = true;

    /**
     * Current curl_multi handle instance.
     *
     * @var Object
     */
    private $curlMultiHandle;

    /**
     * Client constructor.
     *
     * @param AuthProviderInterface $authProvider
     * @param bool $isProductionEnv
     */
    public function __construct(AuthProviderInterface $authProvider, bool $isProductionEnv = false)
    {
        $this->authProvider = $authProvider;
        $this->isProductionEnv = $isProductionEnv;
    }

    /**
     * Push notifications to APNs.
     *
     * @return ApnsResponseInterface[]
     */
    public function push(): array
    {
        if (!$this->curlMultiHandle) {
            $this->curlMultiHandle = curl_multi_init();

            if (!defined('CURLPIPE_MULTIPLEX')) {
                define('CURLPIPE_MULTIPLEX', 2);
            }

            curl_multi_setopt($this->curlMultiHandle, CURLMOPT_PIPELINING, CURLPIPE_MULTIPLEX);
            curl_multi_setopt($this->curlMultiHandle, CURLMOPT_MAX_HOST_CONNECTIONS, $this->maxConcurrentConnections);
        }

        $mh = $this->curlMultiHandle;

        $i = 0;
        while (!empty($this->notifications) && $i++ < $this->nbConcurrentRequests) {
            $notification = array_pop($this->notifications);
            curl_multi_add_handle($mh, $this->prepareHandle($notification));
        }

        // Clear out curl handle buffer
        do {
            $execrun = curl_multi_exec($mh, $running);
        } while ($execrun === CURLM_CALL_MULTI_PERFORM);

        // Continue processing while we have active curl handles
        while ($running > 0 && $execrun === CURLM_OK) {
            // Block until data is available
            $select_fd = curl_multi_select($mh);
            // If select returns -1 while running, wait 250 microseconds before continuing
            // Using curl_multi_timeout would be better but it isn't available in PHP yet
            // https://php.net/manual/en/function.curl-multi-select.php#115381
            if ($running && $select_fd === -1) {
                usleep(250);
            }

            // Continue to wait for more data if needed
            do {
                $execrun = curl_multi_exec($mh, $running);
            } while ($execrun === CURLM_CALL_MULTI_PERFORM);

            // Start reading results
            while ($done = curl_multi_info_read($mh)) {
                $handle = $done['handle'];

                $result = curl_multi_getcontent($handle);

                // find out which token the response is about
                $token = curl_getinfo($handle, CURLINFO_PRIVATE);

                $responseParts = explode("\r\n\r\n", $result, 2);
                $headers = '';
                $body = '';
                if (isset($responseParts[0])) {
                    $headers = $responseParts[0];
                }
                if (isset($responseParts[1])) {
                    $body = $responseParts[1];
                }

                $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                $responseCollection[] = new Response($statusCode, $headers, $body, $token);
                curl_multi_remove_handle($mh, $handle);
                curl_close($handle);

                if (!empty($this->notifications)) {
                    $notification = array_pop($this->notifications);
                    curl_multi_add_handle($mh, $this->prepareHandle($notification));
                }
            }
        }

        if ($this->autoCloseConnections) {
            curl_multi_close($mh);
            $this->curlMultiHandle = null;
        }

        return $responseCollection;
    }

    /**
     * Prepares a curl handle from a Notification object.
     *
     * @param Notification $notification
     */
    private function prepareHandle(Notification $notification)
    {
        $request = new Request($notification, $this->isProductionEnv);
        $ch = curl_init();

        $this->authProvider->authenticateClient($request);

        curl_setopt_array($ch, $request->getOptions());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->getDecoratedHeaders());

        // store device token to identify response
        curl_setopt($ch, CURLOPT_PRIVATE, $notification->getDeviceToken());

        return $ch;
    }

    /**
     * Add notification in queue for sending.
     *
     * @param Notification $notification
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * Add several notifications in queue for sending.
     *
     * @param Notification[] $notifications
     */
    public function addNotifications(array $notifications)
    {
        foreach ($notifications as $notification) {
            if (in_array($notification, $this->notifications, true)) {
                continue;
            }

            $this->addNotification($notification);
        }
    }

    /**
     * Get already added notifications.
     *
     * @return Notification[]
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }

    /**
     * Close the current curl multi handle.
     */
    public function close()
    {
        if ($this->curlMultiHandle) {
            curl_multi_close($this->curlMultiHandle);
            $this->curlMultiHandle = null;
        }
    }

    /**
     * Set the number of concurrent requests sent through the multiplexed connections.
     *
     * @param int $nbConcurrentRequests
     */
    public function setNbConcurrentRequests($nbConcurrentRequests)
    {
        $this->nbConcurrentRequests = $nbConcurrentRequests;
    }


    /**
     * Set the number of maximum concurrent connections established to the APNS servers.
     *
     * @param int $nbConcurrentRequests
     */
    public function setMaxConcurrentConnections($maxConcurrentConnections)
    {
        $this->maxConcurrentConnections = $maxConcurrentConnections;
    }

    /**
     * Set wether or not the client should automatically close the connections. Apple recommends keeping
     * connections open if you send more than a few notification per minutes.
     *
     * @param bool $nbConcurrentRequests
     */
    public function setAutoCloseConnections($autoCloseConnections)
    {
        $this->autoCloseConnections = $autoCloseConnections;
    }
}
