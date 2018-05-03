<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Framework\Registry;
use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Vertex\Tax\Model\Calculation\VertexCalculator;

/**
 * Assists with retrieving Giftwrapping taxes
 *
 * @see Giftwrapping
 */
class TotalPlugin
{
    /** @var Registry */
    private $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Register identifiers for giftwrapping items
     *
     * MEQP2 Warning: Unused Parameter - Parameter expected from plugin
     *
     * @see Giftwrapping::collect()
     * @todo Convert to afterCollect once we only support Magento 2.2+
     *
     * @param Giftwrapping $subject
     * @param \Closure $proceed
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Quote\Address\Total $total
     * @return Giftwrapping
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject is a necessary part of a plugin
     * @throws \RuntimeException
     */
    public function aroundCollect(
        Giftwrapping $subject,
        \Closure $proceed,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Quote\Address\Total $total
    ) {
        $result = $proceed($quote, $shippingAssignment, $total);

        foreach ($shippingAssignment->getItems() as $item) {
            $gwItemCode = 'gw' . $item->getItemId();
            $this->registry->register(
                VertexCalculator::VERTEX_QUOTE_ITEM_ID_PREFIX . $gwItemCode,
                Giftwrapping::CODE_ITEM_GW_PREFIX . '_' . $item->getItemId(),
                true
            );
        }

        return $result;
    }
}
