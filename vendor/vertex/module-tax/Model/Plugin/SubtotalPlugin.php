<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Model\Sales\Total\Quote\Subtotal;
use Vertex\Tax\Model\VertexUsageDeterminer;

/**
 * Prevent Subtotal Tax calculation when Vertex is enabled
 *
 * @see Subtotal
 */
class SubtotalPlugin
{
    /** @var VertexUsageDeterminer */
    private $usageDeterminer;

    /**
     * @param VertexUsageDeterminer $usageDeterminer
     */
    public function __construct(VertexUsageDeterminer $usageDeterminer)
    {
        $this->usageDeterminer = $usageDeterminer;
    }

    /**
     * Prevent Subtotal Tax calculation when Vertex is enabled
     *
     * Vertex doesn't support post-tax discounts, so this isn't necessary
     *
     * @param Subtotal $subject
     * @param callable $super
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Quote\Address\Total $total
     * @return QuoteDetailsItemInterface
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject is a necessary part of a plugin
     */
    public function aroundCollect(
        Subtotal $subject,
        callable $super,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Quote\Address\Total $total
    ) {
        $storeId = $quote->getStoreId();
        $address = $shippingAssignment->getShipping()->getAddress();
        if (!$this->usageDeterminer->shouldUseVertex(
            $storeId,
            $address,
            $quote->getCustomerId(),
            $quote->isVirtual()
        )) {
            // Allows forward compatibility with argument additions
            $arguments = func_get_args();
            array_splice($arguments, 0, 2);
            return call_user_func_array($super, $arguments);
        }
    }
}
