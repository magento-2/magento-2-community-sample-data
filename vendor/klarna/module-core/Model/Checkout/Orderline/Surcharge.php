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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Tax\Model\Calculation;
use Klarna\Core\Model\Fpt\Rate;

/**
 * Class Surcharge
 *
 * @package Klarna\Core\Model\Checkout\Orderline
 */
class Surcharge extends AbstractLine
{

    const ITEM_TYPE_SURCHARGE = 'surcharge';
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
        /** @var \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote $object */
        $object = $checkout->getObject();
        $store = $object->getStore();

        if (!$this->configHelper->isFptEnabled($store) || !$this->configHelper->getDisplayInSubtotalFpt($store)) {
            return $this;
        }
        $result = $this->rate->getFptTax($object);

        $checkout->addData(array(
            'surcharge_unit_price'   => $this->helper->toApiFloat($result['tax']),
            'surcharge_total_amount' => $this->helper->toApiFloat($result['tax']),
            'surcharge_reference'    => implode(',', $result['reference']),
            'surcharge_name'         => implode(',', $result['name'])
        ));
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
        if ($checkout->getSurchargeUnitPrice()) {
            $checkout->addOrderLine(array(
                'type'             => self::ITEM_TYPE_SURCHARGE,
                'reference'        => $checkout->getSurchargeReference(),
                'name'             => $checkout->getSurchargeName(),
                'quantity'         => 1,
                'unit_price'       => $checkout->getSurchargeUnitPrice(),
                'tax_rate'         => 0,
                'total_amount'     => $checkout->getSurchargeTotalAmount(),
                'total_tax_amount' => 0,
            ));
        }

        return $this;
    }
}
