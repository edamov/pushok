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

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Promise;

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
        $client = new GuzzleClient();

        $promises = [];
        foreach ($this->notifications as $k => $notification) {
            $request = new Request($notification, $this->isProductionEnv);

            $this->authProvider->authenticateClient($request);

            $promises[] = $client->postAsync($request->getUri(), [
                'version' => 2.0,
                'http_errors' => false,
                'body' => $request->getBody(),
                'headers' => $request->getHeaders()
            ]);
        }

        $results = Promise\settle($promises)->wait();

        $responseCollection = $this->mapResults($results);

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

    private function mapResults($results)
    {
        $responseCollection = [];

        foreach ($results as $result) {
            if (isset($result['value'])) {
                $responseCollection[] = Response::createFromPsrInterface($result['value']);
            } elseif (isset($result['reason'])) {
                $responseCollection[] = $this->mapErrorResponse($result['reason']);
            }
        }

        return $responseCollection;
    }

    private function mapErrorResponse(TransferException $error)
    {
        if ($error instanceof RequestException) {
            if ($error->hasResponse()) {
                return Response::createFromPsrInterface($error->getResponse());
            }

            return new Response(0, null, $error->getHandlerContext()['error']);
        }

        throw new \Exception($error->getMessage(), $error->getCode());
    }
}
