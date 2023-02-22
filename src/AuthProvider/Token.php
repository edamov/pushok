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

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Algorithm\ES512;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Pushok\AuthProviderInterface;
use Pushok\Request;

/**
 * Class Token
 * @package Pushok\AuthProvider
 *
 * @see http://bit.ly/communicating-with-apns
 */
class Token implements AuthProviderInterface
{
    /**
     * Generated auth token.
     *
     * @var string
     */
    private $token;

    /**
     * Path to p8 private key.
     *
     * @var string|null
     */
    private $privateKeyPath;

    /**
     * Private key data.
     *
     * @var string|null
     */
    private $privateKeyContent;

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
     * - private_key_content
     * - private_key_secret
     *
     * @param array $options
     */
    private function __construct(array $options)
    {
        $this->keyId = $options['key_id'];
        $this->teamId = $options['team_id'];
        $this->appBundleId = $options['app_bundle_id'];
        $this->privateKeyPath = $options['private_key_path'] ?? null;
        $this->privateKeyContent = $options['private_key_content'] ?? null;
        $this->privateKeySecret = $options['private_key_secret'] ?? null;
    }

    /**
     * Create Token Auth Provider.
     *
     * @param array $options
     * @return Token
     */
    public static function create(array $options): Token
    {
        $token = new self($options);
        $token->token = $token->generate();

        return $token;
    }

    /**
     * Use previously generated token.
     *
     * @param string $tokenString
     * @param array $options
     * @return Token
     */
    public static function useExisting(string $tokenString, array $options): Token
    {
        $token = new self($options);
        $token->token = $tokenString;

        return $token;
    }

    /**
     * Authenticate client.
     *
     * @param Request $request
     */
    public function authenticateClient(Request $request)
    {
        $request->addHeaders([
            'apns-topic' => $this->generateApnsTopic($request->getHeaders()['apns-push-type']),
            'Authorization' => 'bearer ' . $this->token,
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

            case 'liveactivity':
                return $this->appBundleId . '.push-type.liveactivity';

            case 'complication':
                return $this->appBundleId . '.complication';

            case 'fileprovider':
                return $this->appBundleId . '.pushkit.fileprovider';

            default:
                return $this->appBundleId;
        }
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
     * Generate private EC key.
     *
     * @return JWK
     */
    private function generatePrivateECKey(): JWK
    {
        if ($this->privateKeyContent) {
            $content = $this->privateKeyContent;
        } elseif ($this->privateKeyPath) {
            $content = \file_get_contents($this->privateKeyPath);
        } else {
            throw new \InvalidArgumentException('Unable to find private key.');
        }

        return JWKFactory::createFromKey($content, $this->privateKeySecret, [
            'kid' => $this->keyId,
            'alg' => 'ES512',
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
     * @param JWK $privateECKey
     * @return array
     */
    private function getProtectedHeader(JWK $privateECKey): array
    {
        return [
            'alg' => 'ES512',
            'kid' => $privateECKey->get('kid'),
        ];
    }

    /**
     * Generate new token.
     *
     * @return string
     */
    private function generate(): string
    {
        $algorithmManager = new AlgorithmManager([
          new ES512(),
        ]);

        $jwsBuilder = new JWSBuilder($algorithmManager);
        $payload = json_encode($this->getClaimsPayload(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $privateECKey = $this->generatePrivateECKey();

        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($privateECKey, $this->getProtectedHeader($privateECKey))
            ->build();

        $serializer = new CompactSerializer();
        $this->token = $serializer->serialize($jws);

        return $this->token;
    }
}
