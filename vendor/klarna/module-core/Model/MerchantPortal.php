<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model;

use Klarna\Core\Helper\ConfigHelper;
use Klarna\Core\Api\OrderInterface as KlarnaOrder;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;

/**
 * Class MerchantPortal
 *
 * @package Klarna\Core\Model
 */
class MerchantPortal
{
    const MERCHANT_PORTAL_US = "https://orders.us.portal.klarna.com/";
    const MERCHANT_PORTAL_EU = "https://orders.eu.portal.klarna.com/";

    /**
     * @var \Klarna\Core\Helper\ConfigHelper
     */
    private $configHelper;

    /**
     * MerchantPortal Model.
     *
     * @param ConfigHelper $configHelper
     */
    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * Get Merchant Portal link for order
     *
     * @param MageOrder   $mageOrder
     * @param KlarnaOrder $klarnaOrder
     * @return string
     */
    public function getOrderMerchantPortalLink(MageOrder $mageOrder, KlarnaOrder $klarnaOrder)
    {
        $store = $mageOrder->getStore();
        $merchantId = $this->configHelper->getApiConfig('merchant_id', $store);
        $apiVersion = $this->configHelper->getApiConfig('api_version', $store);
        $url = self::MERCHANT_PORTAL_EU;
        if (in_array($apiVersion, ['na', 'kp_na'])) {
            $url = self::MERCHANT_PORTAL_US;
        }
        $merchantIdArray = explode("_", $merchantId);
        $url .= "merchants/" . $merchantIdArray[0] . "/orders/" . $klarnaOrder->getKlarnaOrderId();
        return $url;
    }
}
