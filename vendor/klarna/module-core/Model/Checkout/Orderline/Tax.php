<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Checkout\Orderline;

use Klarna\Core\Api\BuilderInterface;
use Magento\Quote\Model\Quote;

/**
 * Generate tax order line details
 */
class Tax extends AbstractLine
{
    /**
     * Checkout item types
     */
    const ITEM_TYPE_TAX = 'sales_tax';

    /**
     * Collect totals process.
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     * @throws \Klarna\Core\Exception
     */
    public function collect(BuilderInterface $checkout)
    {
        $object = $checkout->getObject();

        if (!$this->klarnaConfig->isSeparateTaxLine($object->getStore())) {
            return $this;
        }

        if ($object instanceof Quote) {
            $object->collectTotals();
            $totalTax = $object->isVirtual() ? $object->getBillingAddress()->getBaseTaxAmount()
                : $object->getShippingAddress()->getBaseTaxAmount();
        } else {
            $totalTax = $object->getBaseTaxAmount();
        }

        $checkout->addData(
            [
                'tax_unit_price'   => $this->helper->toApiFloat($totalTax),
                'tax_total_amount' => $this->helper->toApiFloat($totalTax)
            ]
        );

        return $this;
    }

    /**
     * Add order details to checkout request
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getTaxUnitPrice()) {
            $checkout->addOrderLine(
                [
                    'type'             => self::ITEM_TYPE_TAX,
                    'reference'        => __('Sales Tax'),
                    'name'             => __('Sales Tax'),
                    'quantity'         => 1,
                    'unit_price'       => $checkout->getTaxUnitPrice(),
                    'tax_rate'         => 0,
                    'total_amount'     => $checkout->getTaxTotalAmount(),
                    'total_tax_amount' => 0,
                ]
            );
        }

        return $this;
    }
}
