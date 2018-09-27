<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;
use Magento\Tax\Model\Sales\Total\Quote\Subtotal;

/**
 * Retrieve special ItemType values
 */
class ItemType
{
    /** @var ModuleManager */
    private $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Determine if the Giftwrapping module is enabled
     *
     * @return bool
     */
    private function isGiftWrappingEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_GiftWrapping');
    }

    /**
     * Get the Product Item Type
     *
     * @return string
     */
    public function product()
    {
        return Subtotal::ITEM_TYPE_PRODUCT;
    }

    /**
     * Get the Shipping Item Type
     *
     * @return string
     */
    public function shipping()
    {
        return Subtotal::ITEM_TYPE_SHIPPING;
    }

    /**
     * Get the Giftwrap Item type - or false if unavailable
     *
     * @return bool|string
     */
    public function giftWrap()
    {
        return $this->isGiftWrappingEnabled() ? Giftwrapping::ITEM_TYPE : false;
    }

    /**
     * Get the Order-level Giftwrap Item type - or false if unavailable
     *
     * @return bool|string
     */
    public function orderGiftWrap()
    {
        return $this->isGiftWrappingEnabled() ? Giftwrapping::CODE_QUOTE_GW : false;
    }

    /**
     * Get the Printed Card Item type - or false if unavailable
     *
     * @return bool|string
     */
    public function orderPrintedCard()
    {
        return $this->isGiftWrappingEnabled() ? Giftwrapping::CODE_PRINTED_CARD : false;
    }
}
