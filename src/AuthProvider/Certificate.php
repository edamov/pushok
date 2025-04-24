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
        # OpenSSL (versions 0.9.3 and later) also support "P12" for PKCS#12-encoded files.
        # see https://curl.se/libcurl/c/CURLOPT_SSLCERTTYPE.html
        $ext = pathinfo($this->certificatePath, \PATHINFO_EXTENSION);
        if (preg_match('#^(der|p12)$#i', $ext)) {
            $request->addOptions(
                [
                    CURLOPT_SSLCERTTYPE => strtoupper($ext)
                ]
            );
        }
        $request->addOptions(
            [
                CURLOPT_SSLCERT        => $this->certificatePath,
                CURLOPT_SSLCERTPASSWD  => $this->certificateSecret,
                CURLOPT_SSL_VERIFYPEER => true
            ]
        );
        $request->addHeaders([
            'apns-topic' => $this->generateApnsTopic($request->getHeaders()['apns-push-type']),
        ]);
    }

    /**
     * Generate a correct apns-topic string
     *
     * @param string $pushType
     * @return string
     */
    public function generateApnsTopic(string $pushType)
    {
        switch ($pushType) {
            case 'voip':
                return $this->appBundleId . '.voip';

            case 'complication':
                return $this->appBundleId . '.complication';

            case 'fileprovider':
                return $this->appBundleId . '.pushkit.fileprovider';

            default:
                return $this->appBundleId;
        }
    }
}
