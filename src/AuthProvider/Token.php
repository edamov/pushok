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

final class Token implements AuthProviderInterface
{
    const HASH_ALGORITHM = 'ES256';

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

        $this->generatePrivateECKey();
    }

    public function authenticateClient($request)
    {
        // TODO: Implement authenticateClient() method.
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

    public function generate()
    {
        $token = JWSFactory::createJWSToCompactJSON(
            $this->getClaimsPayload(),
            $this->privateECKey,
            $this->getProtectedHeader()
        );

        return $token;
    }
}