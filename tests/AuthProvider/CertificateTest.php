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
use Pushok\AuthProviderInterface;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Request;

class CertificateTest extends TestCase
{
    public function testCreatingCertificateAuthProvider()
    {
        $options = [];
        $options['certificate_path'] = __DIR__ . '/../files/certificate.pem';
        $options['certificate_secret'] = 'secret';
        $authProvider = Certificate::create($options);

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
    }

    public function testCreatingCertificateAuthProviderWithAppBundleId()
    {
        $options = [];
        $options['certificate_path'] = __DIR__ . '/../files/certificate.pem';
        $options['certificate_secret'] = 'secret';
        $options['app_bundle_id'] = 'com.apple.test';
        $authProvider = Certificate::create($options);

        $request = $this->createRequest();
        $authProvider->authenticateClient($request);

        $this->assertSame($request->getOptions()[CURLOPT_SSLCERT], $options['certificate_path']);
        $this->assertSame($request->getOptions()[CURLOPT_SSLCERTPASSWD], $options['certificate_secret']);
        $this->assertSame($request->getOptions()[CURLOPT_SSLCERTPASSWD], $options['certificate_secret']);

        $this->assertSame($request->getHeaders()["apns-topic"], $options['app_bundle_id']);
    }

    private function createRequest(): Request
    {
        $notification = new Notification(Payload::create(), '123');
        $request = new Request($notification, $production = false);

        return $request;
    }
}
