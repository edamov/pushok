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

/**
 * Class Certificate
 * @package Pushok\AuthProvider
 *
 * @see http://bit.ly/communicating-with-apns
 */
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

    private function __construct()
    {
    }

    /**
     * Create Certificate Auth provider.
     *
     * @param array $options
     * @return Certificate
     */
    public static function create(array $options): Certificate
    {
        $certificate = new self();
        $certificate->certificatePath = $options['certificate_path'];
        $certificate->certificateSecret = $options['certificate_secret'];

        return $certificate;
    }

    /**
     * Authenticate client.
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
