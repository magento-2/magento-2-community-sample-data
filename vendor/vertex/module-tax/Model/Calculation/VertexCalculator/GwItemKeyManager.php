<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Calculation\VertexCalculator;

use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\TaxRegistry;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;

/**
 * Storage utility for gift wrapping item-level tax information that persists across a tax calculation cycle.
 */
class GwItemKeyManager
{
    /** @var TaxRegistry */
    private $taxRegistry;

    /**
     * @param TaxRegistry $taxRegistry
     */
    public function __construct(TaxRegistry $taxRegistry)
    {
        $this->taxRegistry = $taxRegistry;
    }

    /**
     * Generate a gift wrapping-item map ID for the given quote item.
     *
     * @param AbstractItem $item
     * @return string
     */
    public function generateGwItemMapCode(AbstractItem $item)
    {
        $prefix = class_exists(Giftwrapping::class) ? Giftwrapping::CODE_ITEM_GW_PREFIX : 'item_gw';
        return $prefix . '_' . $item->getItemId();
    }

    /**
     * Retrieve the key for the given item.
     *
     * @param QuoteDetailsItemInterface $item
     * @return string|null
     */
    public function get(QuoteDetailsItemInterface $item)
    {
        return $this->taxRegistry->lookup($this->generateItemStorageKey($item));
    }

    /**
     * Write the given quote item ID to storage.
     *
     * @param QuoteDetailsItemInterface $item
     * @param AbstractItem $quoteItem
     * @return void
     */
    public function set(QuoteDetailsItemInterface $item, AbstractItem $quoteItem)
    {
        $cacheKey = $this->generateItemStorageKey($item);
        $this->taxRegistry->unregister($cacheKey);
        $this->taxRegistry->register($cacheKey, $this->generateGwItemMapCode($quoteItem));
    }

    /**
     * Generate a unique storage key for the given item.
     *
     * @param QuoteDetailsItemInterface $item
     * @return string
     */
    private function generateItemStorageKey(QuoteDetailsItemInterface $item)
    {
        return sha1(
            'gw'
            . VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX
            . $item->getItemId()
            . '_'
            . $item->getGwId()
        );
    }
}
