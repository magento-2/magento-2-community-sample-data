<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Model\Sales\Total\Quote\Subtotal;
use Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager;
use Vertex\Tax\Model\Calculation\VertexCalculator\GwItemKeyManager;

/**
 * Assists with retrieving tax information for items during subtotal routine
 *
 * @see Subtotal
 */
class SubtotalPlugin
{
    /** @var GwItemKeyManager */
    private $gwItemKeyManager;

    /** @var ItemKeyManager */
    private $itemKeyManager;

    /**
     * @param ItemKeyManager $itemKeyManager
     * @param GwItemKeyManager $gwItemKeyManager
     */
    public function __construct(
        ItemKeyManager $itemKeyManager,
        GwItemKeyManager $gwItemKeyManager
    ) {
        $this->itemKeyManager = $itemKeyManager;
        $this->gwItemKeyManager = $gwItemKeyManager;
    }

    /**
     * Register line item identifier with Vertex
     *
     * MEQP2 Warning: Unused Parameter - Parameter expected from plugin
     *
     * @see Subtotal::mapItem()
     * @todo Convert to afterMapItem once we only support Magento 2.2+
     *
     * @param Subtotal $subtotal
     * @param \Closure $proceed
     * @param QuoteDetailsItemInterfaceFactory $itemDataObjectFactory
     * @param AbstractItem $item
     * @param bool $priceIncludesTax
     * @param bool $useBaseCurrency
     * @param string $parentCode
     * @return QuoteDetailsItemInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject is a necessary part of a plugin
     * @throws \RuntimeException
     */
    public function aroundMapItem(
        Subtotal $subtotal,
        \Closure $proceed,
        QuoteDetailsItemInterfaceFactory $itemDataObjectFactory,
        AbstractItem $item,
        $priceIncludesTax,
        $useBaseCurrency,
        $parentCode = null
    ) {
        /** @var QuoteDetailsItemInterface $itemDataObject */
        $itemDataObject = $proceed($itemDataObjectFactory, $item, $priceIncludesTax, $useBaseCurrency, $parentCode);

        $this->itemKeyManager->set($itemDataObject, $item);

        if ($item->getGwId()) {
            $this->gwItemKeyManager->set($itemDataObject, $item);
        }

        return $itemDataObject;
    }
}
