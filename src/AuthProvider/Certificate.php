<?php

/*
 * This file is part of the Pushok package.
 *
 * (c) Arthur Edamov <edamov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pushok\AuthProvider;

use Pushok\AuthProviderInterface;

class Certificate implements AuthProviderInterface
{
    /**
     * Path to certificate.
     *
     * @var string
     */
    private $certificatePath;

    /**
     * Certificate secret.
     *
     * @var string
     */
    private $certificateSecret;

    /**
     * Certificate constructor.
     *
     * @param string $certificatePath
     * @param string|null $certificateSecret
     */
    public function __construct(string $certificatePath, string $certificateSecret = null)
    {
        $this->certificatePath = $certificatePath;
        $this->certificateSecret = $certificateSecret;
    }

    /**
     * Authenticate client
     *
     * @param resource $curlHandle
     */
    public function authenticateClient($curlHandle)
    {
        curl_setopt_array($curlHandle, [
            CURLOPT_SSLCERT => $this->certificatePath,
            CURLOPT_SSLCERTPASSWD => $this->certificateSecret,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
    }
}