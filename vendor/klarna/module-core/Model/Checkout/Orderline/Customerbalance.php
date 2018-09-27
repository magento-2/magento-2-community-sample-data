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

/**
 * Generate order line details for customer balance
 */
class Customerbalance extends AbstractLine
{
    /**
     * {@inheritdoc}
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $checkout->getObject();
        $totals = $quote->getTotals();

        if (!is_array($totals)) {
            return $this;
        }

        if (!isset($totals['customerbalance']) && $quote->getCustomerBalanceAmountUsed()) {
            $totals['customerbalance'] = $this->dataObjectFactory->create([
                'data' => [
                    'value' => -1 * $quote->getCustomerBalanceAmountUsed(),
                    'title' => 'Customer Balance',
                    'code'  => 'customerbalance',
                ]
            ]);
        }

        if (isset($totals['customerbalance'])) {
            $total = $totals['customerbalance'];
            $value = $this->helper->toApiFloat($total->getValue());

            $checkout->addData([
                'customerbalance_unit_price'   => $value,
                'customerbalance_tax_rate'     => 0,
                'customerbalance_total_amount' => $value,
                'customerbalance_tax_amount'   => 0,
                'customerbalance_title'        => $total->getTitle(),
                'customerbalance_reference'    => $total->getCode()

            ]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getCustomerbalanceTotalAmount()) {
            $checkout->addOrderLine([
                'type'             => Discount::ITEM_TYPE_DISCOUNT,
                'reference'        => $checkout->getCustomerbalanceReference(),
                'name'             => $checkout->getCustomerbalanceTitle(),
                'quantity'         => 1,
                'unit_price'       => $checkout->getCustomerbalanceUnitPrice(),
                'tax_rate'         => $checkout->getCustomerbalanceTaxRate(),
                'total_amount'     => $checkout->getCustomerbalanceTotalAmount(),
                'total_tax_amount' => $checkout->getCustomerbalanceTaxAmount(),
            ]);
        }

        return $this;
    }
}
