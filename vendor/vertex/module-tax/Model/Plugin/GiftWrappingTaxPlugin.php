<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Framework\Registry;
use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;
use Magento\GiftWrapping\Model\Total\Quote\Tax\GiftwrappingAfterTax;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Vertex\Tax\Model\Calculation\VertexCalculator;

/**
 * Processes Giftwrapping Tax
 *
 * @see Giftwrapping
 */
class GiftWrappingTaxPlugin
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
     * Add Vertex tax to giftwrapping item
     *
     * MEQP2 Warning: Unused parameter $subtotal expected from plugins
     *
     * @see Giftwrapping::collect()
     *
     * @param GiftwrappingAfterTax $subject
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Quote\Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject is a necessary part of a plugin
     */
    public function beforeCollect(
        GiftwrappingAfterTax $subject,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Quote\Address\Total $total
    ) {
        $extraTaxableDetails = $total->getData('extra_taxable_details');
        $itemGwType = Giftwrapping::ITEM_TYPE;
        $vertexTaxItems = $this->registry->registry(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY);

        if (isset($extraTaxableDetails[$itemGwType])) {
            $extraTaxableDetails[$itemGwType] = $this->processWrappingForItems(
                $vertexTaxItems,
                $extraTaxableDetails[$itemGwType]
            );
        }

        $total->setData('extra_taxable_details', $extraTaxableDetails);

        return [$quote, $shippingAssignment, $total];
    }

    /**
     * Update wrapping tax total for items
     *
     * @param array $vertexTaxItems
     * @param array $itemTaxDetails
     * @return array
     */
    private function processWrappingForItems($vertexTaxItems, $itemTaxDetails)
    {
        foreach ($itemTaxDetails as $gwItems) {
            foreach ($gwItems as &$gwItem) {
                if (isset($vertexTaxItems[$gwItem['code']])) {
                    $vertexTaxItems[$gwItem['code']];
                }
            }
        }

        return $itemTaxDetails;
    }
}
