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
 * Generate order line details for gift card
 */
class Giftcard extends AbstractLine
{
    /**
     * {@inheritdoc}
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $checkout->getObject();
        $totals = $quote->getTotals();

        if (!is_array($totals) || !isset($totals['giftcardaccount'])) {
            return $this;
        }
        $total = $totals['giftcardaccount'];
        $amount = $total->getValue();
        if ($amount !== 0) {
            $amount = $quote->getGiftCardsAmountUsed();
            $value = -1 * $this->helper->toApiFloat($amount);

            $checkout->addData([
                'giftcardaccount_unit_price'   => $value,
                'giftcardaccount_tax_rate'     => 0,
                'giftcardaccount_total_amount' => $value,
                'giftcardaccount_tax_amount'   => 0,
                'giftcardaccount_title'        => $total->getTitle(),
                'giftcardaccount_reference'    => $total->getCode()
            ]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getGiftcardaccountTotalAmount()) {
            $checkout->addOrderLine([
                'type'             => Discount::ITEM_TYPE_DISCOUNT,
                'reference'        => $checkout->getGiftcardaccountReference(),
                'name'             => $checkout->getGiftcardaccountTitle(),
                'quantity'         => 1,
                'unit_price'       => $checkout->getGiftcardaccountUnitPrice(),
                'tax_rate'         => $checkout->getGiftcardaccountTaxRate(),
                'total_amount'     => $checkout->getGiftcardaccountTotalAmount(),
                'total_tax_amount' => $checkout->getGiftcardaccountTaxAmount(),
            ]);
        }

        return $this;
    }
}
