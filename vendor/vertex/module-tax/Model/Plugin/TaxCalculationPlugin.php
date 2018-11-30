<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\Data\QuoteDetailsInterface;
use Magento\Tax\Api\TaxCalculationInterface;
use Vertex\Tax\Model\Calculator;
use Vertex\Tax\Model\VertexUsageDeterminer;

/**
 * Handle tax calculation through Vertex
 */
class TaxCalculationPlugin
{
    /** @var Calculator */
    private $calculator;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var VertexUsageDeterminer */
    private $usageDeterminer;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Calculator $calculator
     * @param VertexUsageDeterminer $usageDeterminer
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Calculator $calculator,
        VertexUsageDeterminer $usageDeterminer
    ) {
        $this->storeManager = $storeManager;
        $this->calculator = $calculator;
        $this->usageDeterminer = $usageDeterminer;
    }

    /**
     * Use Vertex to calculate tax if it can be used
     *
     * @see TaxCalculationInterface::calculateTax()
     * @param TaxCalculationInterface $subject
     * @param callable $super
     * @param QuoteDetailsInterface $quoteDetails
     * @param string|null $storeId
     * @param bool $round
     * @return \Magento\Tax\Api\Data\TaxDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \InvalidArgumentException
     */
    public function aroundCalculateTax(
        TaxCalculationInterface $subject,
        callable $super,
        QuoteDetailsInterface $quoteDetails,
        $storeId = null,
        $round = true
    ) {
        $storeId = $this->getStoreId($storeId);
        if (!$this->useVertex($quoteDetails, $storeId, $this->isVirtual($quoteDetails))) {
            // Allows forward compatibility with argument additions
            $arguments = func_get_args();
            array_splice($arguments, 0, 2);
            return call_user_func_array($super, $arguments);
        }

        return $this->calculator->calculateTax($quoteDetails, $storeId, $round);
    }

    /**
     * Retrieve current Store ID
     *
     * @param string|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreId($storeId)
    {
        return $storeId ?: $this->storeManager->getStore()->getStoreId();
    }

    /**
     * Determine whether a quote is virtual or not
     *
     * This determination is made by whether or not the quote has a shipping
     * item
     *
     * @param QuoteDetailsInterface $quoteDetails
     * @return bool
     */
    private function isVirtual(QuoteDetailsInterface $quoteDetails)
    {
        $items = $quoteDetails->getItems();
        foreach ($items as $item) {
            if ($item->getType() === 'shipping') {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine whether or not to use Vertex
     *
     * We make this determination based on the UsageDeterminer result as well as whether or not any items on the
     * quote actually have a price.
     *
     * @param QuoteDetailsInterface $quoteDetails
     * @param string|null $storeId
     * @param bool $isVirtual
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function useVertex(QuoteDetailsInterface $quoteDetails, $storeId, $isVirtual)
    {
        $anItemHasPrice = false;
        foreach ($quoteDetails->getItems() as $item) {
            if ($item->getUnitPrice()) {
                $anItemHasPrice = true;
            }
        }
        return $anItemHasPrice
            && $this->usageDeterminer->shouldUseVertex(
                $storeId,
                $quoteDetails->getShippingAddress(),
                $quoteDetails->getCustomerId(),
                $isVirtual
            );
    }
}
