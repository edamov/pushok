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

class CertificateTest extends TestCase
{
    public function testCreatingCertificateAuthProvider()
    {
        $options = [];
        $options['certificate_path'] = __DIR__ . '/../files/certificate.pem';
        $options['certificate_secret'] = 'secret';
        $authProvider = AuthProvider\Certificate::create($options);

        $this->assertInstanceOf(AuthProviderInterface::class, $authProvider);
    }
}
