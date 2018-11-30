<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Api\Data\QuoteDetailsItemExtensionFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemExtensionInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use Vertex\Tax\Model\Config;

/**
 * Plugins to the Common Tax Collector
 */
class CommonTaxCollectorPlugin
{
    /** @var Config */
    private $config;

    /** @var QuoteDetailsItemExtensionFactory */
    private $extensionFactory;

    /**
     * @param QuoteDetailsItemExtensionFactory $extensionFactory
     * @param Config $config
     */
    public function __construct(QuoteDetailsItemExtensionFactory $extensionFactory, Config $config)
    {
        $this->extensionFactory = $extensionFactory;
        $this->config = $config;
    }

    /**
     * Add a created SKU for shipping to the QuoteDetailsItem
     *
     * @param CommonTaxCollector $subject
     * @param callable $super
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param bool $useBaseCurrency
     * @return QuoteDetailsItemInterface
     */
    public function aroundGetShippingDataObject(
        CommonTaxCollector $subject,
        callable $super,
        ShippingAssignmentInterface $shippingAssignment,
        $total,
        $useBaseCurrency
    ) {
        // Allows forward compatibility with argument additions
        $arguments = func_get_args();
        array_splice($arguments, 0, 2);

        /** @var QuoteDetailsItemInterface[] $quoteItems */
        $itemDataObject = call_user_func_array($super, $arguments);

        $store = $this->getStoreCodeFromShippingAssignment($shippingAssignment);

        $shipping = $shippingAssignment->getShipping();
        if ($itemDataObject === null || $shipping === null || !$this->config->isVertexActive($store)) {
            return $itemDataObject;
        }

        if ($shipping->getMethod() === null && $total->getShippingTaxCalculationAmount() == 0) {
            // If there's no method and a $0 price then there's no need for an empty shipping tax item
            return null;
        }

        $extensionAttributes = $this->getExtensionAttributes($itemDataObject);
        $extensionAttributes->setVertexProductCode($shippingAssignment->getShipping()->getMethod());

        return $itemDataObject;
    }

    /**
     * Add product SKU to a QuoteDetailsItem
     *
     * @see CommonTaxCollector::mapItem()
     * @param CommonTaxCollector $subject
     * @param callable $super
     * @param QuoteDetailsItemInterfaceFactory $dataObjectFactory
     * @param AbstractItem $item
     * @param bool $priceIncludesTax
     * @param bool $useBaseCurrency
     * @param string|null $parentCode
     * @return QuoteDetailsItemInterface
     */
    public function aroundMapItem(
        CommonTaxCollector $subject,
        callable $super,
        $dataObjectFactory,
        AbstractItem $item,
        $priceIncludesTax,
        $useBaseCurrency,
        $parentCode = null
    ) {
        // Allows forward compatibility with argument additions
        $arguments = func_get_args();
        array_splice($arguments, 0, 2);

        /** @var QuoteDetailsItemInterface $taxData */
        $taxData = call_user_func_array($super, $arguments);

        if ($this->config->isVertexActive($item->getStore())) {
            $extensionData = $this->getExtensionAttributes($taxData);
            $extensionData->setVertexProductCode($item->getProduct()->getSku());
        }

        return $taxData;
    }

    /**
     * Add a created SKU and update the tax class of Item-level Giftwrap
     *
     * @param CommonTaxCollector $subject
     * @param callable $super
     * @param QuoteDetailsItemInterfaceFactory $dataObjectFactory
     * @param AbstractItem $item
     * @param $priceIncludesTax
     * @param $useBaseCurrency
     * @return QuoteDetailsItemInterface[]
     */
    public function aroundMapItemExtraTaxables(
        CommonTaxCollector $subject,
        callable $super,
        $dataObjectFactory,
        AbstractItem $item,
        $priceIncludesTax,
        $useBaseCurrency
    ) {
        // Allows forward compatibility with argument additions
        $arguments = func_get_args();
        array_splice($arguments, 0, 2);

        /** @var QuoteDetailsItemInterface[] $quoteItems */
        $quoteItems = call_user_func_array($super, $arguments);

        $store = $item->getStore();

        if (!$this->config->isVertexActive($store)) {
            return $quoteItems;
        }

        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getType() !== 'item_gw') {
                continue;
            }
            $productSku = $item->getProduct()->getSku();
            $taxClassId = $this->config->getGiftWrappingItemClass($store);
            $gwPrefix = $this->config->getGiftWrappingItemCodePrefix($store);

            // Set the Product Code
            $extensionData = $this->getExtensionAttributes($quoteItem);
            $extensionData->setVertexProductCode($gwPrefix . $productSku);

            // Change the Tax Class ID
            $quoteItem->setTaxClassId($taxClassId);
            $taxClassKey = $quoteItem->getTaxClassKey();
            if ($taxClassKey && $taxClassKey->getType() === TaxClassKeyInterface::TYPE_ID) {
                $quoteItem->getTaxClassKey()->setValue($taxClassId);
            }
        }
        return $quoteItems;
    }

    /**
     * Retrieve an extension attribute object for the QuoteDetailsItem
     *
     * @param QuoteDetailsItemInterface $taxData
     * @return QuoteDetailsItemExtensionInterface
     */
    private function getExtensionAttributes(QuoteDetailsItemInterface $taxData)
    {
        $extensionAttributes = $taxData->getExtensionAttributes();
        if ($extensionAttributes instanceof QuoteDetailsItemExtensionInterface) {
            return $extensionAttributes;
        }

        $extensionAttributes = $this->extensionFactory->create();
        $taxData->setExtensionAttributes($extensionAttributes);
        return $extensionAttributes;
    }

    /**
     * Retrieve the Store ID from a Shipping Assignment
     *
     * This is the same way the Magento_Tax module gets the store when its needed - we have a problem, though, where
     * getQuote isn't part of the AddressInterface, and I don't particularly trust all the getters to not unexpectedly
     * return NULL.
     *
     * @param ShippingAssignmentInterface|null $shippingAssignment
     * @return string|null
     */
    private function getStoreCodeFromShippingAssignment(ShippingAssignmentInterface $shippingAssignment = null)
    {
        return $shippingAssignment !== null
        && $shippingAssignment->getShipping() !== null
        && $shippingAssignment->getShipping()->getAddress() !== null
        && method_exists($shippingAssignment->getShipping()->getAddress(), 'getQuote')
        && $shippingAssignment->getShipping()->getAddress()->getQuote() !== null
            ? $shippingAssignment->getShipping()->getAddress()->getQuote()->getStoreId()
            : null;
    }
}
