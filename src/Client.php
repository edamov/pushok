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
     * @return array
     */
    public function push(): array
    {
        $curlHandle = curl_init();

        $responseCollection = [];
        foreach ($this->notifications as $notification) {
            $request = new Request($notification, $this->isProductionEnv);

            $this->authProvider->authenticateClient($request);

            $result = $this->send($curlHandle, $request);

            list($headers, $body) = explode("\r\n\r\n", $result, 2);

            $statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

            $responseCollection[] = new Response($statusCode, $headers, $body);
        }

        curl_close($curlHandle);

        return $responseCollection;
    }

    /**
     * Send request.
     *
     * @param $curlHandle
     * @param Request $request
     *
     * @return mixed Return the result on success, false on failure
     */
    private function send($curlHandle, Request $request)
    {
        curl_setopt_array($curlHandle, $request->getOptions());
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $request->getHeaders());

        return curl_exec($curlHandle);
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
        $this->notifications = array_merge($this->notifications, $notifications);
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
