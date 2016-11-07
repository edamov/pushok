<?php

namespace Pushok\AuthProvider;

use PHPUnit\Framework\TestCase;
use Pushok\AuthProvider;
use Pushok\AuthProviderInterface;

class TokenTest extends TestCase
{
    public function testCreatingTokenAuthProvider()
    {
        $authProvider = AuthProvider\Token::create($this->getOptions());

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
        $this->assertTrue(is_string($authProvider->get()));
    }

    public function testUseExistingToken()
    {
        $token = 'eyJhbGciOiJFUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAifQ.eyJpc3MiOiIxMjM0NTY3ODkwIiwiaWF0IjoxNDc4NTE0NDk4fQ.' .
            'YxR8Hw--Hp8YH8RF2QDiwOjmGhTC_7g2DpbnzKQZ8Sj20-q12LrLrAMafcuxf97CTHl9hCVere0vYrzcLmGV-A';

        $options = [
            'key_id' => '1234567890',
            'team_id' => '1234567890',
            'app_bundle_id' => 'com.app.Test',
            'private_key_path' => __DIR__ . '../files/private_key.p8',
            'private_key_secret' => null
        ];

        $authProvider = AuthProvider\Token::useExisting($token, $options);

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
        $this->assertEquals($token, $authProvider->get());
    }

    private function getOptions()
    {
        return [
            'key_id' => '1234567890',
            'team_id' => '1234567890',
            'app_bundle_id' => 'com.app.Test',
            'private_key_path' => __DIR__ . '/../files/private_key.p8',
            'private_key_secret' => null
        ];
    }
}
