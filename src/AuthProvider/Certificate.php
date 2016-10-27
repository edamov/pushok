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

final class Certificate implements AuthProviderInterface
{
    /**
     * Path to certificate
     *
     * @var string
     */
    private $certificatePath;

    /**
     * Certificate constructor.
     * @param string $certificatePath
     */
    public function __construct(string $certificatePath)
    {
        $this->certificatePath = $certificatePath;
    }

    public function authenticateClient()
    {
        // TODO: Implement authenticateClient() method.
    }
}