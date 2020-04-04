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
        $options = $this->getOptions();
        $authProvider = Certificate::create($options);

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
    }

    public function testAuthenticatingClient()
    {
        $options = $this->getOptions();
        $authProvider = Certificate::create($options);

        $request = $this->createRequest();
        $authProvider->authenticateClient($request);

        $this->assertSame($request->getOptions()[CURLOPT_SSLCERT], $options['certificate_path']);
        $this->assertSame($request->getOptions()[CURLOPT_SSLCERTPASSWD], $options['certificate_secret']);
    }

    public function testVoipApnsTopic()
    {
        $options = $this->getOptions();
        $authProvider = Certificate::create($options);

        $request = $this->createRequest('voip');
        $authProvider->authenticateClient($request);

        $this->assertSame($request->getHeaders()['apns-topic'], $options['app_bundle_id'] . '.voip');
    }

    public function testComplicationApnsTopic()
    {
        $options = $this->getOptions();
        $authProvider = Certificate::create($options);

        $request = $this->createRequest('complication');
        $authProvider->authenticateClient($request);

        $this->assertSame($request->getHeaders()['apns-topic'], $options['app_bundle_id'] . '.complication');
    }

    public function testFileproviderApnsTopic()
    {
        $options = $this->getOptions();
        $authProvider = Certificate::create($options);

        $request = $this->createRequest('fileprovider');
        $authProvider->authenticateClient($request);

        $this->assertSame($request->getHeaders()['apns-topic'], $options['app_bundle_id'] . '.pushkit.fileprovider');
    }

    private function getOptions()
    {
        return [
            'certificate_path' => __DIR__ . '/../files/certificate.pem',
            'certificate_secret' => 'secret',
            'app_bundle_id' => 'com.apple.test',
        ];
    }

    private function createRequest(string $pushType = 'alert'): Request
    {
        $notification = new Notification(Payload::create()->setPushType($pushType), '123');
        $request = new Request($notification, $production = false);

        return $request;
    }
}
