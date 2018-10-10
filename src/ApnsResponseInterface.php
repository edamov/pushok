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
 * Interface ApnsResponseInterface
 * @package Pushok
 */
interface ApnsResponseInterface
{
    /**
     * Get APNs Id
     *
     * @return string
     */
    public function getApnsId();

    /**
     * Get status code.
     *
     * @return int|null
     */
    public function getStatusCode(): int;

    /**
     * Get reason phrase.
     *
     * @return string
     */
    public function getReasonPhrase(): string;

    /**
     * Get error reason.
     *
     * @return string
     */
    public function getErrorReason(): string;

    /**
     * Get error description.
     *
     * @return string
     */
    public function getErrorDescription(): string;

    /**
     * Get timestamp for a status 410 error
     *
     * @return string
     */
    public function get410Timestamp(): string;
}
