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

use Jose\Factory\JWKFactory;
use Jose\Factory\JWSFactory;
use Pushok\AuthProviderInterface;

class Token implements AuthProviderInterface
{
    const HASH_ALGORITHM = 'ES256';

    /**
     * Generated auth token
     *
     * @var string
     */
    private $token;

    /**
     * Path to p8 private key
     *
     * @var string
     */
    private $privateKeyPath;

    /**
     * @var string|null
     */
    private $privateKeySecret;

    /**
     * @var \Jose\Object\JWKInterface
     */
    private $privateECKey;

    /**
     * The Key ID obtained from Apple developer account
     *
     * @var string
     */
    private $keyId;

    /**
     * The Team ID obtained from Apple developer account
     *
     * @var string
     */
    private $teamId;

    /**
     * The bundle ID for app
     *
     * @var string
     */
    private $appBundleId;

    /**
     * Token constructor.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        //todo: validate configs

        $this->keyId = $configs['key_id'];
        $this->teamId = $configs['team_id'];
        $this->privateKeyPath = $configs['private_key_path'];
        $this->privateKeySecret = $configs['private_key_secret'];
        $this->appBundleId = $configs['app_bundle_id'];

        $this->generatePrivateECKey();
    }

    private function generatePrivateECKey()
    {
        $this->privateECKey = JWKFactory::createFromKeyFile($this->privateKeyPath, $this->privateKeySecret, [
            'kid' => $this->keyId,
            'alg' => self::HASH_ALGORITHM,
            'use' => 'sig'
        ]);
    }

    private function getClaimsPayload()
    {
        return [
            'iss' => $this->teamId,
            'iat' => time(),
        ];
    }

    private function getProtectedHeader()
    {
        return [
            'alg' => self::HASH_ALGORITHM,
            'kid' => $this->privateECKey->get('kid'),
        ];
    }

    public function authenticateClient($curlHandle)
    {
        $headers = [
            "apns-topic: " . $this->appBundleId,
            'Authorization: bearer ' . $this->token
        ];

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
    }

    public function generate()
    {
        $this->token = JWSFactory::createJWSToCompactJSON(
            $this->getClaimsPayload(),
            $this->privateECKey,
            $this->getProtectedHeader()
        );

        return $this->token;
    }

    public function get()
    {
        return $this->token;
    }
}