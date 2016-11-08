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

/**
 * Interface AuthProviderInterface
 * @package Pushok
 */
interface AuthProviderInterface
{
    /**
     * Authenticate client
     *
     * @param Request $request
     * @return void
     */
    public function authenticateClient(Request $request);
}
