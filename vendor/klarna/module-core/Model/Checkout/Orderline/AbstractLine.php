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
use Klarna\Core\Api\OrderLineInterface;
use Klarna\Core\Helper\DataConverter;
use Klarna\Core\Helper\KlarnaConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Klarna order line abstract
 */
abstract class AbstractLine implements OrderLineInterface
{
    /**
     * @var DataConverter
     */
    protected $helper;

    /**
     * Order line code name
     *
     * @var string
     */
    protected $code;

    /**
     * Order line is used to calculate a total
     *
     * For example, shipping total, order total, or discount total
     *
     * This should be set to false for items like order items
     *
     * @var bool
     */
    protected $isTotalCollector = true;

    /**
     * @var Calculation
     */
    protected $calculator;

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    /**
     * @var KlarnaConfig
     */
    protected $klarnaConfig;

    /**
     * AbstractLine constructor.
     *
     * @param DataConverter        $helper
     * @param Calculation          $calculator
     * @param ScopeConfigInterface $config
     * @param DataObjectFactory    $dataObjectFactory
     * @param KlarnaConfig         $klarnaConfig
     */
    public function __construct(
        DataConverter $helper,
        Calculation $calculator,
        ScopeConfigInterface $config,
        DataObjectFactory $dataObjectFactory,
        KlarnaConfig $klarnaConfig
    ) {
        $this->helper = $helper;
        $this->calculator = $calculator;
        $this->config = $config;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->klarnaConfig = $klarnaConfig;
    }

    /**
     * Check if the order line is for an order item or a total collector
     *
     * @return boolean
     */
    public function isIsTotalCollector()
    {
        return $this->isTotalCollector;
    }

    /**
     * Retrieve code name
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code name
     *
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get tax amount for discount
     *
     * @param Item[] $items
     * @param array  $total
     * @param float  $taxRate
     * @return float
     */
    public function getDiscountTaxAmount($items, $total, $taxRate)
    {
        $taxAmount = 0;
        foreach ($items as $item) {
            if ($item->getBaseDiscountAmount() == 0) {
                continue;
            }
            $taxAmount += $item->getDiscountTaxCompensationAmount();
        }

        if ($taxAmount === 0) {
            if ($taxRate > 1) {
                $taxRate = $taxRate / 100;
            }
            $taxAmount = -($total->getValue() - ($total->getValue() / (1 + $taxRate)));
        }
        return $taxAmount;
    }

    /**
     * Get the tax rate for the discount order line
     *
     * @param BuilderInterface $checkout
     * @param Item[]           $items
     *
     * @return float
     */
    public function getDiscountTaxRate($checkout, $items = [])
    {
        $discountTaxRate = $this->getPartialOrderDiscount($items);
        if ($discountTaxRate) {
            return $discountTaxRate;
        }

        $discountTaxRate = $this->getFullOrderDiscount($checkout);
        if ($discountTaxRate) {
            return $discountTaxRate;
        }
        return $checkout->getDiscountTaxRate();
    }

    /**
     * @param $items
     * @return int
     */
    private function getPartialOrderDiscount($items)
    {
        if ($items === null || !count($items)) {
            return false;
        }
        $itemTaxRates = [];
        $totalsIncludingTax = [];
        $totalsExcludingTax = [];
        foreach ($items as $item) {
            if ($item->getBaseDiscountAmount() == 0) {
                continue;
            }
            $totalsIncludingTax[] = $item->getRowTotalInclTax();
            $totalsExcludingTax[] = $item->getRowTotalInclTax() - $item->getTaxAmount();
            $itemTaxRates[] = $item->getTaxPercent();
        }
        $itemTaxRates = array_unique($itemTaxRates);
        $taxRateCount = count($itemTaxRates);

        if (1 < $taxRateCount) {
            $discountTaxRate = ((array_sum($totalsIncludingTax) / array_sum($totalsExcludingTax)) - 1);
            return $this->helper->toApiFloat($discountTaxRate);
        }
        if (1 === $taxRateCount) {
            return $this->helper->toApiFloat(reset($itemTaxRates) / 100);
        }
        // Every item has a discount, so fall through to secondary logic
        return false;
    }

    /**
     * @param $checkout
     * @return int
     */
    private function getFullOrderDiscount($checkout)
    {
        if (!$checkout->getItems()) {
            return false;
        }
        $itemTaxRates = [];
        $totalsIncludingTax = [];
        $totalsExcludingTax = [];
        foreach ($checkout->getItems() as $item) {
            $totalsIncludingTax[] = $item['total_amount'];
            $totalsExcludingTax[] = $item['total_amount'] - $item['total_tax_amount'];
            $itemTaxRates[] = isset($item['tax_rate']) ? ($item['tax_rate'] * 1) : 0;
        }

        $itemTaxRates = array_unique($itemTaxRates);
        $taxRateCount = count($itemTaxRates);

        if (1 < $taxRateCount) {
            $discountTaxRate = ((array_sum($totalsIncludingTax) / array_sum($totalsExcludingTax)) - 1);
            return $this->helper->toApiFloat($discountTaxRate);
        }
        if (1 === $taxRateCount) {
            return reset($itemTaxRates);
        }
        return false;
    }

    /**
     * Calculate shipping tax rate for an object
     *
     * @param BuilderInterface $checkout
     * @param StoreInterface $store
     * @return float
     */
    protected function calculateShippingTax(BuilderInterface $checkout, StoreInterface $store)
    {
        $object = $checkout->getObject();
        $request = $this->calculator->getRateRequest(
            $object->getShippingAddress(),
            $object->getBillingAddress(),
            $object->getCustomerTaxClassId(),
            $store
        );
        $taxRateId = $this->config->getValue(
            TaxConfig::CONFIG_XML_PATH_SHIPPING_TAX_CLASS,
            ScopeInterface::SCOPE_STORES,
            $store
        );

        return $this->calculator->getRate($request->setProductClassId($taxRateId));
    }
}
