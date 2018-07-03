<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\ResourceModel\Quote;

/**
 * Class Collection
 *
 * @package Klarna\Kp\Model\ResourceModel\Quote
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init(\Klarna\Kp\Model\Quote::class, \Klarna\Kp\Model\ResourceModel\Quote::class);
    }
}
