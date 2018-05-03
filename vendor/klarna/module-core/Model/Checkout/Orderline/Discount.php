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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Generate order lines for discounts
 *
 * @author  Joe Constant <joe.constant@klarna.com>
 * @author  Jason Grim <jason.grim@klarna.com>
 */
class Discount extends AbstractLine
{
    /**
     * Checkout item type
     */
    const ITEM_TYPE_DISCOUNT = 'discount';

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
        $address = $this->getAddress($object);
        $store = $this->getStore($object, $address);
        $totals = $address->getTotals();

        if (is_array($totals) && isset($totals['discount'])) {
            $discountInfo = $this->processDiscountFromTotals($checkout, $totals, $object, $store);
            $checkout->addData($discountInfo);
        } elseif ($this->isDiscounted($object)) {
            $discountInfo = $this->processDiscountWithoutTotals($checkout, $object, $store);
            $checkout->addData($discountInfo);
        }

        return $this;
    }

    /**
     * @param $object
     * @return mixed
     */
    private function getAddress($object)
    {
        $address = $object->getShippingAddress();
        if ($address) {
            return $address;
        }
        return $object->getBillingAddress();
    }

    /**
     * @param $object
     * @param $address
     * @return mixed
     */
    private function getStore($object, $address)
    {
        $store = $object->getStore();
        if (!$store && $address->getQuote()) {
            $store = $address->getQuote()->getStore();
        }
        return $store;
    }

    /**
     * @param BuilderInterface         $checkout
     * @param Total                    $totals
     * @param Quote|CreditMemo|Invoice $object
     * @param Store                    $store
     * @return array
     * @throws \Klarna\Core\Exception
     */
    private function processDiscountFromTotals(BuilderInterface $checkout, $totals, $object, $store)
    {
        $total = $totals['discount'];

        $taxRate = $this->getDiscountTaxRate($checkout, $object->getAllItems());
        $taxAmount = $this->getDiscountTaxAmount($object->getAllItems(), $total, $taxRate);

        $amount = -$total->getValue();
        $taxRate = ($taxAmount / ($amount - $taxAmount)) * 100;

        $unitPrice = $amount;
        $totalAmount = $amount;
        if ($this->klarnaConfig->isSeparateTaxLine($store) || $this->isTaxBeforeDiscount($store)) {
            $taxRate = 0;
            $taxAmount = 0;
        } else {
            if ($this->isPriceExcludesVat($store)) {
                $unitPrice += $taxAmount;
                $totalAmount += $taxAmount;
            }
        }
        return [
            'discount_unit_price'   => -$this->helper->toApiFloat($unitPrice),
            'discount_tax_rate'     => $this->helper->toApiFloat($taxRate),
            'discount_total_amount' => -$this->helper->toApiFloat($totalAmount),
            'discount_tax_amount'   => -$this->helper->toApiFloat($taxAmount),
            'discount_title'        => (string)$total->getTitle(),
            'discount_reference'    => $total->getCode()

        ];
    }

    /**
     * Determine if product price excludes VAT or not
     *
     * @param Store $store
     * @return bool
     */
    private function isTaxBeforeDiscount($store = null)
    {
        $scope = ($store === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        return !$this->config->isSetFlag('tax/calculation/apply_after_discount', $scope, $store);
    }

    /**
     * Determine if product price excludes VAT or not
     *
     * @param Store $store
     * @return bool
     */
    private function isPriceExcludesVat($store = null)
    {
        $scope = ($store === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        return !$this->config->isSetFlag('tax/calculation/price_includes_tax', $scope, $store);
    }

    /**
     * Determine if quote/invoice/creditmemo contains a discount
     *
     * @param $object
     * @return bool
     */
    private function isDiscounted($object)
    {
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($object->getDiscountAmount())) {
            return true;
        }
        if (!empty($object->getBaseSubtotalWithDiscount()) &&
            $object->getBaseSubtotalWithDiscount() !== $object->getBaseSubtotal()
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param BuilderInterface         $checkout
     * @param Quote|CreditMemo|Invoice $object
     * @param Store                    $store
     * @return array
     * @throws \Klarna\Core\Exception
     */
    private function processDiscountWithoutTotals(BuilderInterface $checkout, $object, $store)
    {
        $discountLabel = (string)__('Discount');
        if ($object->getDiscountDescription()) {
            $discountLabel = (string)__('Discount (%1)', $object->getDiscountDescription());
        }

        $amount = $object->getDiscountAmount();
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($amount) && !empty($object->getBaseSubtotalWithDiscount())) {
            $amount = $object->getBaseSubtotal() - $object->getBaseSubtotalWithDiscount();
        }
        $taxRate = $this->getDiscountTaxRate($checkout, $object->getAllVisibleItems());
        if ($taxRate > 1) {
            $taxRate = $taxRate / 100;
        }
        $taxAmount = -($amount - ($amount / (1 + $taxRate)));

        $unitPrice = $amount;
        $totalAmount = $amount;
        if ($this->klarnaConfig->isSeparateTaxLine($store)) {
            $taxRate = 0;
            $taxAmount = 0;
        } else {
            if ($this->isPriceExcludesVat($store)) {
                $unitPrice += $taxAmount;
                $totalAmount += $taxAmount;
            }
        }
        return [
            'discount_unit_price'   => -abs($this->helper->toApiFloat($unitPrice)),
            'discount_tax_rate'     => $this->helper->toApiFloat($taxRate * 100),
            'discount_total_amount' => -abs($this->helper->toApiFloat($totalAmount)),
            'discount_tax_amount'   => $this->helper->toApiFloat($taxAmount),
            'discount_title'        => $discountLabel,
            'discount_reference'    => self::ITEM_TYPE_DISCOUNT

        ];
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
        if ($checkout->getDiscountReference() && $checkout->getDiscountTotalAmount() !== 0) {
            $checkout->addOrderLine(
                [
                    'type'             => self::ITEM_TYPE_DISCOUNT,
                    'reference'        => $checkout->getDiscountReference(),
                    'name'             => $checkout->getDiscountTitle(),
                    'quantity'         => 1,
                    'unit_price'       => $checkout->getDiscountUnitPrice(),
                    'tax_rate'         => $checkout->getDiscountTaxRate(),
                    'total_amount'     => $checkout->getDiscountTotalAmount(),
                    'total_tax_amount' => $checkout->getDiscountTaxAmount(),
                ]
            );
        }

        return $this;
    }
}
