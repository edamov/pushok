<?php

/*
 * This file is part of the Pushok package.
 *
 * (c) Arthur Edamov <edamov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pushok;

interface AuthProviderInterface
{
    /**
     * Authenticate client
     *
     * @param $curlHandle resource a cURL handle
     * @return void
     */
    public function authenticateClient($curlHandle);
}
