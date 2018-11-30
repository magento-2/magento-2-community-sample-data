<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Quote\Model\Quote;
use Magento\Tax\Api\Data\QuoteDetailsItemExtensionFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemExtensionInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\Tax\Model\Sales\Total\Quote\Tax;
use Vertex\Tax\Model\Config;

/**
 * Plugins to the Tax Total
 */
class TaxPlugin
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
        $this->config = $config;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Add Vertex product codes and custom tax classes to extra taxables
     *
     * @param Tax $subject
     * @param callable $super
     * @param QuoteDetailsItemInterfaceFactory $itemDataObjectFactory
     * @param Quote\Address $address
     * @param bool $useBaseCurrency
     * @return QuoteDetailsItemInterface[]
     */
    public function aroundMapQuoteExtraTaxables(
        Tax $subject,
        callable $super,
        $itemDataObjectFactory,
        Quote\Address $address,
        $useBaseCurrency
    ) {
        // Allows forward compatibility with argument additions
        $arguments = func_get_args();
        array_splice($arguments, 0, 2);

        /** @var QuoteDetailsItemInterface[] $items */
        $items = call_user_func_array($super, $arguments);

        $store = $address->getQuote()->getStore();

        if (!$this->config->isVertexActive($store)) {
            return $items;
        }

        foreach ($items as $item) {
            switch ($item->getType()) {
                case 'quote_gw':
                    $sku = $this->config->getGiftWrappingOrderCode($store);
                    $taxClassId = $this->config->getGiftWrappingOrderClass($store);
                    break;
                case 'printed_card_gw':
                    $sku = $this->config->getPrintedGiftcardCode($store);
                    $taxClassId = $this->config->getPrintedGiftcardClass($store);
                    break;
                default:
                    continue 2;
            }
            $extensionAttributes = $this->getExtensionAttributes($item);
            $extensionAttributes->setVertexProductCode($sku);
            $item->setTaxClassId($taxClassId);
            if ($item->getTaxClassKey() && $item->getTaxClassKey()->getType() === TaxClassKeyInterface::TYPE_ID) {
                $item->getTaxClassKey()->setValue($taxClassId);
            }
        }

        return $items;
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
}
