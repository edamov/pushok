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
     * Array of messages.
     *
     * @var Message[]
     */
    private $messages = [];

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
     * Push messages to APNs.
     *
     * @return array
     */
    public function push(): array
    {
        $curlHandle = curl_init();

        $responseCollection = [];
        foreach ($this->messages as $message) {
            $request = new Request($message, $this->isProductionEnv);

            $this->authProvider->authenticateClient($curlHandle);

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

        return curl_exec($curlHandle);
    }

    /**
     * Add message in queue for sending.
     *
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * Add several messages in queue for sending.
     *
     * @param Message[] $messages
     */
    public function addMessages(array $messages)
    {
        $this->messages = array_merge($this->messages, $messages);
    }
}