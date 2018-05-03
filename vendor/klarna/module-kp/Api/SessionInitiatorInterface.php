<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

use Magento\Quote\Api\Data\CartInterface;

/**
 * Interface SessionInitiatorInterface
 *
 * @package Klarna\Kp\Api
 */
interface SessionInitiatorInterface
{

    /**
     * @param CartInterface $quote
     * @param string $code
     * @return bool
     */
    public function checkAvailable($quote, $code);
}
