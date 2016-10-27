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

class Client
{
    /**
     * @var Message[]
     */
    private $messages;

    /**
     * @var AuthProviderInterface
     */
    private $authProvider;

    /**
     * @var bool
     */
    private $isProductionEnv;

    public function __construct(AuthProviderInterface $authProvider, bool $isProductionEnv = false)
    {
        $this->authProvider = $authProvider;
        $this->isProductionEnv = $isProductionEnv;
    }

    public function send()
    {
        $curlHandle = curl_init();

        foreach ($this->messages as $message) {
            $request = new Request($curlHandle, $message, $this->isProductionEnv);

            $this->authProvider->authenticateClient($curlHandle);

            $response[] = $request->send();
        }

        curl_close($curlHandle);

        return $response;
    }

    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
    }

    public function addMessages($messages)
    {
        $this->messages = array_merge($this->messages, $messages);
    }
}