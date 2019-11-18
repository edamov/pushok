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

use PHPUnit\Framework\TestCase;
use Pushok\AuthProvider;
use Pushok\AuthProviderInterface;

class TokenTest extends TestCase
{
    public function testCreatingTokenAuthProvider()
    {
        $options = $this->getOptions();
        $options['private_key_path'] = __DIR__ . '/../files/private_key.p8';
        $authProvider = AuthProvider\Token::create($options);

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
        $this->assertTrue(is_string($authProvider->get()));
    }

    private function getOptions()
    {
        return [
            'key_id' => '1234567890',
            'team_id' => '1234567890',
            'app_bundle_id' => 'com.app.Test',
        ];
    }

    public function testCreatingTokenAuthProviderWithKeyContent()
    {
        $options = $this->getOptions();

        $options['private_key_content'] = file_get_contents(
            implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'files', 'private_key.p8'])
        );

        $authProvider = AuthProvider\Token::create($options);

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
        $this->assertTrue(is_string($authProvider->get()));
    }

    public function testCreatingTokenAuthProviderWithoutKey()
    {
        $this->expectException(\InvalidArgumentException::class);

        $options = $this->getOptions();
        AuthProvider\Token::create($options);
    }

    public function testUseExistingToken()
    {
        $token = 'eyJhbGciOiJFUzI1NiIsImtpZCI6IjEyMzQ1Njc4OTAifQ.eyJpc3MiOiIxMjM0NTY3ODkwIiwiaWF0IjoxNDc4NTE0NDk4fQ.' .
            'YxR8Hw--Hp8YH8RF2QDiwOjmGhTC_7g2DpbnzKQZ8Sj20-q12LrLrAMafcuxf97CTHl9hCVere0vYrzcLmGV-A';

        $options = $this->getOptions();
        $authProvider = AuthProvider\Token::useExisting($token, $options);

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
        $this->assertEquals($token, $authProvider->get());
    }
}
