<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Helper;

/**
 * Class DataConverter
 *
 * @package Klarna\Core\Helper
 */
class DataConverter
{
    /**
     * Prepare float for API call
     *
     * @param float $float
     *
     * @return int
     */
    public function toApiFloat($float)
    {
        return round($float * 100);
    }
}
