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

    public function push()
    {
        $curlHandle = curl_init();

        $responsesCollection = [];
        foreach ($this->messages as $message) {
            $request = new Request($message, $this->isProductionEnv);

            $this->authProvider->authenticateClient($curlHandle);

            $result = $this->send($curlHandle, $request);

            list($headers, $body) = explode("\r\n\r\n", $result, 2);

            $statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

            $responsesCollection[] = new Response($statusCode, $headers, $body);
        }

        curl_close($curlHandle);

        return $responsesCollection;
    }

    private function send($curlHandle, Request $request)
    {
        curl_setopt_array($curlHandle, $request->getOptions());

        return curl_exec($curlHandle);
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