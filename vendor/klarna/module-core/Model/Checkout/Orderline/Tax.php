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
use Klarna\Core\Helper\ConfigHelper;
use Klarna\Core\Helper\DataConverter;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Core\Model\Fpt\Rate;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote;
use Magento\Tax\Model\Calculation;

/**
 * Generate tax order line details
 */
class Tax extends AbstractLine
{
    /**
     * Checkout item types
     */
    const ITEM_TYPE_TAX = 'sales_tax';

    /** @var Rate $rate */
    private $rate;

    /** @var ConfigHelper $configHelper */
    private $configHelper;

    /**
     * AbstractLine constructor.
     *
     * @param DataConverter        $helper
     * @param Calculation          $calculator
     * @param ScopeConfigInterface $config
     * @param DataObjectFactory    $dataObjectFactory
     * @param KlarnaConfig         $klarnaConfig
     * @param ConfigHelper         $configHelper
     * @param Rate                 $rate
     */
    public function __construct(
        DataConverter $helper,
        Calculation $calculator,
        ScopeConfigInterface $config,
        DataObjectFactory $dataObjectFactory,
        KlarnaConfig $klarnaConfig,
        ConfigHelper $configHelper,
        Rate $rate
    ) {
        parent::__construct(
            $helper,
            $calculator,
            $config,
            $dataObjectFactory,
            $klarnaConfig
        );

        $this->configHelper = $configHelper;
        $this->rate = $rate;
    }

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

        $totalTax = $object->getBaseTaxAmount();
        if ($object instanceof Quote) {
            $object->collectTotals();
            $totalTax = $object->isVirtual() ? $object->getBillingAddress()->getBaseTaxAmount()
                : $object->getShippingAddress()->getBaseTaxAmount();
        }

        /** @var \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote $object */
        $object = $checkout->getObject();
        $store = $object->getStore();

        if ($this->configHelper->isFptEnabled($store) && !$this->configHelper->getDisplayInSubtotalFpt($store)) {
            $fptResult = $this->rate->getFptTax($object);
            $totalTax += $fptResult['tax'];
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
