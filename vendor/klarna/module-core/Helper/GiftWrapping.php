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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\Calculation;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Core\Helper\DataConverter;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Framework\DataObject;

/**
 * Class GiftWrapping
 *
 * @package Klarna\Core\Helper
 */
class GiftWrapping extends AbstractHelper
{
    /**
     * @var Calculation
     */
    private $calculator;

    /**
     * @var DataConverter
     */
    private $dataConverter;

    /**
     * Gift wrapping tax class
     */
    const XML_PATH_TAX_CLASS_GW = 'tax/classes/wrapping_tax_class';

    /**
     * GiftWrapping Helper.
     *
     * @param Context $context
     * @param Calculation $calculator
     */
    public function __construct(
        Context $context,
        Calculation $calculator,
        DataConverter $dataConverter
    ) {
        parent::__construct($context);
        $this->calculator = $calculator;
        $this->dataConverter = $dataConverter;
    }

    /**
     * Calculate gift wrapping tax rate for an object
     *
     * @param Quote|Order $object
     * @param StoreInterface $store
     * @return float
     */
    public function getGiftWrappingTaxRate($object, StoreInterface $store)
    {
        //if address object doesn't contain valid data tax rate will be mis-calculated to 0
        //in this case use null instead
        $billingAddress = $this->isAddressValidForTaxCalculation($object->getBillingAddress())
            ? $object->getBillingAddress() : null;
        $shippingAddress = $this->isAddressValidForTaxCalculation($object->getBillingAddress())
            ? $object->getBillingAddress() : null;

        $request = $this->calculator->getRateRequest(
            $shippingAddress,
            $billingAddress,
            null,
            $store
        );
        $taxRateId = $this->scopeConfig->getValue(
            self::XML_PATH_TAX_CLASS_GW,
            ScopeInterface::SCOPE_STORES,
            $store
        );

        return $this->calculator->getRate($request->setProductClassId($taxRateId));
    }

    /**
     * check if address is ready to send for calculating tax
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Quote\Api\Data\AddressInterface $address
     * @return bool
     */
    private function isAddressValidForTaxCalculation($address)
    {
        return (bool)$address->getCountryId();
    }

    /**
     * calculate missing gift wrapping tax
     *
     * @param DataObject $checkout
     *
     * @param Quote $quote
     *
     * @return float|int
     */
    public function getAdditionalGwTax(DataObject $checkout, Quote $quote)
    {
        $klarnaTotal = (int)($checkout->getOrderAmount() ?: $checkout->getData('cart/total_price_including_tax'));
        $quoteTotal = (int)$this->dataConverter->toApiFloat($quote->getGrandTotal());

        if ($klarnaTotal > $quoteTotal) {
            $store = $quote->getStore();
            $taxRate = $this->getGiftWrappingTaxRate($quote, $store);

            if ($quote->getGwId()
                && $quote->getGwBasePrice() > 0
                && $quote->getGwBaseTaxAmount() == 0
                && $taxRate > 0) {
                $gwTotalAmount = $quote->getGwBasePrice() * ((100 + $taxRate) / 100);
                $taxAmount = $gwTotalAmount * ($taxRate / (100 + $taxRate));
                $taxAmount = (int)$this->dataConverter->toApiFloat($taxAmount);
                //additional validation to ensure only gift wrapping tax is missing from quote total
                if ($klarnaTotal == ($quoteTotal + $taxAmount)) {
                    return $taxAmount;
                }
            }
        }
        return 0;
    }
}
