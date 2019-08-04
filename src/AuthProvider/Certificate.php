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
use Pushok\Request;

/**
 * Class Certificate
 * @package Pushok\AuthProvider
 *
 * @see     http://bit.ly/communicating-with-apns
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

    /**
     * The bundle ID for app obtained from Apple developer account.
     *
     * @var string
     */
    private $appBundleId;

    /**
     * This provider accepts the following options:
     *
     * - certificate_path
     * - certificate_secret
     *
     * @param array $options
     */
    private function __construct(array $options)
    {
        $this->certificatePath   = $options['certificate_path'] ;
        $this->certificateSecret = $options['certificate_secret'];
        $this->appBundleId       = $options['app_bundle_id'] ?? null;
    }

    /**
     * Create Certificate Auth provider.
     *
     * @param array $options
     * @return Certificate
     */
    public static function create(array $options): Certificate
    {
        return new self($options);
    }

    /**
     * Authenticate client.
     *
     * @param Request $request
     */
    public function authenticateClient(Request $request)
    {
        $request->addOptions(
            [
                CURLOPT_SSLCERT        => $this->certificatePath,
                CURLOPT_SSLCERTPASSWD  => $this->certificateSecret,
                CURLOPT_SSL_VERIFYPEER => true
            ]
        );
        $request->addHeaders([
            "apns-topic" => $this->appBundleId
        ]);
    }
}