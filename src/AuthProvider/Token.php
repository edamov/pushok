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
use Jose\Object\JWKInterface;
use Pushok\AuthProviderInterface;

class Token implements AuthProviderInterface
{
    /**
     * Hash alghorithm for generating auth token.
     */
    const HASH_ALGORITHM = 'ES256';

    /**
     * Generated auth token.
     *
     * @var string
     */
    private $token;

    /**
     * Path to p8 private key.
     *
     * @var string
     */
    private $privateKeyPath;

    /**
     * Private key secret.
     *
     * @var string|null
     */
    private $privateKeySecret;

    /**
     * The Key ID obtained from Apple developer account.
     *
     * @var string
     */
    private $keyId;

    /**
     * The Team ID obtained from Apple developer account.
     *
     * @var string
     */
    private $teamId;

    /**
     * The bundle ID for app obtained from Apple developer account.
     *
     * @var string
     */
    private $appBundleId;

    /**
     * This provider accepts the following options:
     *
     * - key_id
     * - team_id
     * - app_bundle_id
     * - private_key_path
     * - private_key_secret
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        //todo: validate configs

        $this->keyId = $options['key_id'];
        $this->teamId = $options['team_id'];
        $this->appBundleId = $options['app_bundle_id'];
        $this->privateKeyPath = $options['private_key_path'];
        $this->privateKeySecret = $options['private_key_secret'] ?: null;

        $this->token = $this->generate();
    }

    /**
     * Generate private EC key.
     *
     * @return JWKInterface
     */
    private function generatePrivateECKey(): JWKInterface
    {
        return JWKFactory::createFromKeyFile($this->privateKeyPath, $this->privateKeySecret, [
            'kid' => $this->keyId,
            'alg' => self::HASH_ALGORITHM,
            'use' => 'sig'
        ]);
    }

    /**
     * Get claims payload.
     *
     * @return array
     */
    private function getClaimsPayload(): array
    {
        return [
            'iss' => $this->teamId,
            'iat' => time(),
        ];
    }

    /**
     * Get protected header.
     *
     * @param JWKInterface $privateECKey
     * @return array
     */
    private function getProtectedHeader(JWKInterface $privateECKey): array
    {
        return [
            'alg' => self::HASH_ALGORITHM,
            'kid' => $privateECKey->get('kid'),
        ];
    }

    /**
     * Authenticate client.
     *
     * @param resource $curlHandle
     */
    public function authenticateClient($curlHandle)
    {
        $headers = [
            "apns-topic: " . $this->appBundleId,
            'Authorization: bearer ' . $this->token
        ];

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Generate new token.
     *
     * @return string
     */
    public function generate(): string
    {
        $privateECKey = $this->generatePrivateECKey();

        $this->token = JWSFactory::createJWSToCompactJSON(
            $this->getClaimsPayload(),
            $privateECKey,
            $this->getProtectedHeader($privateECKey)
        );

        return $this->token;
    }

    /**
     * Get last generated token.
     *
     * @return string
     */
    public function get(): string
    {
        return $this->token;
    }

    /**
     * Set token.
     *
     * @param string $token
     */
    public function set(string $token)
    {
        $this->token = $token;
    }
}