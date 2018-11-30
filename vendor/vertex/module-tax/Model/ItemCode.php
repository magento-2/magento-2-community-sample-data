<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;

/**
 * Retrieve special item codes
 */
class ItemCode
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
     * Get the Giftwrapping Code
     *
     * @return bool|string
     */
    public function giftWrap()
    {
        return $this->isGiftWrappingEnabled() ? Giftwrapping::CODE_QUOTE_GW : false;
    }

    /**
     * Get the Printed Card code
     *
     * @return bool|string
     */
    public function printedCard()
    {
        return $this->isGiftWrappingEnabled() ? Giftwrapping::CODE_PRINTED_CARD : false;
    }
}
