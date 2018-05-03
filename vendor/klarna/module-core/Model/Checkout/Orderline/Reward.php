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
 * Generate order line details for reward
 */
class Reward extends AbstractLine
{
    /**
     * Collect totals process.
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $checkout->getObject();
        $totals = $quote->getTotals();

        if (is_array($totals) && isset($totals['reward'])) {
            $total = $totals['reward'];
            $value = $this->helper->toApiFloat($total->getValue());

            $checkout->addData([
                'reward_unit_price'   => $value,
                'reward_tax_rate'     => 0,
                'reward_total_amount' => $value,
                'reward_tax_amount'   => 0,
                'reward_title'        => (string)$total->getTitle(),
                'reward_reference'    => $total->getCode()

            ]);
        }

        return $this;
    }

    /**
     * Add grand total information to address
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getRewardTotalAmount()) {
            $checkout->addOrderLine([
                'type'             => Discount::ITEM_TYPE_DISCOUNT,
                'reference'        => $checkout->getRewardReference(),
                'name'             => $checkout->getRewardTitle(),
                'quantity'         => 1,
                'unit_price'       => $checkout->getRewardUnitPrice(),
                'tax_rate'         => $checkout->getRewardTaxRate(),
                'total_amount'     => $checkout->getRewardTotalAmount(),
                'total_tax_amount' => $checkout->getRewardTaxAmount(),
            ]);
        }

        return $this;
    }
}
