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
     * @param Quote $quote
     * @param StoreInterface $store
     * @return float
     */
    public function getGiftWrappingTaxRate(Quote $quote, StoreInterface $store)
    {
        $request = $this->calculator->getRateRequest(
            $quote->getShippingAddress(),
            $quote->getBillingAddress(),
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
                //additional validation to ensure only gift wrapping tax is missing from quote total
                if ($klarnaTotal == ($quoteTotal + $taxAmount)) {
                    return $taxAmount;
                }
            }
        }
        return 0;
    }
}
