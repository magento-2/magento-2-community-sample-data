<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * Interface ApiObjectInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface ApiObjectInterface
{
    /**
     * Generate array object needed for API call
     *
     * @return array
     */
    public function toArray();
}
