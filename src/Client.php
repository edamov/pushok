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
        $mh = curl_multi_init();

        curl_multi_setopt($mh, CURLMOPT_PIPELINING, CURLPIPE_MULTIPLEX);

        $handles = [];
        foreach ($this->notifications as $k => $notification) {
            $request = new Request($notification, $this->isProductionEnv);
            $handles[] = $ch = curl_init();

            $this->authProvider->authenticateClient($request);

            curl_setopt_array($ch, $request->getOptions());
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request->getDecoratedHeaders());

            curl_multi_add_handle($mh, $ch);
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        $responseCollection = [];
        foreach ($handles as $handle) {
            curl_multi_remove_handle($mh, $handle);
            $result = curl_multi_getcontent($handle);

            list($headers, $body) = explode("\r\n\r\n", $result, 2);
            $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            $responseCollection[] = new Response($statusCode, $headers, $body);
        }

        curl_multi_close($mh);

        return $responseCollection;
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
    public function getNotifications()
    {
        return $this->notifications;
    }
}
