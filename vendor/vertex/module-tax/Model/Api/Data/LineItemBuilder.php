<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data;

use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Builds a {@see LineItemInterface} for use with the Vertex SDK
 */
class LineItemBuilder
{
    /** @var LineItemInterfaceFactory */
    private $factory;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param TaxClassNameRepository $taxClassNameRepository
     * @param LineItemInterfaceFactory $factory
     */
    public function __construct(
        TaxClassNameRepository $taxClassNameRepository,
        LineItemInterfaceFactory $factory
    ) {
        $this->taxClassNameRepository = $taxClassNameRepository;
        $this->factory = $factory;
    }

    /**
     * Build a {@see LineItemInterface} from a {@see QuoteDetailsItemInterface}
     *
     * @param QuoteDetailsItemInterface $item
     * @param int|null $qtyOverride
     * @return LineItemInterface
     */
    public function buildFromQuoteDetailsItem(QuoteDetailsItemInterface $item, $qtyOverride = null)
    {
        $lineItem = $this->createLineItem();

        $sku = $item->getExtensionAttributes() !== null
            ? $item->getExtensionAttributes()->getVertexProductCode()
            : null;

        if ($sku !== null) {
            $lineItem->setProductCode(substr($sku, 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED));
        }

        $taxClassId = $item->getTaxClassKey() && $item->getTaxClassKey()->getType() === TaxClassKeyInterface::TYPE_ID
            ? $item->getTaxClassKey()->getValue()
            : $item->getTaxClassId();

        $lineItem->setProductClass(
            $this->taxClassNameRepository->getById($taxClassId)
        );

        $quantity = (float)($qtyOverride ?: $item->getQuantity());

        $lineItem->setQuantity($quantity);
        $lineItem->setUnitPrice($item->getUnitPrice());

        $rowTotal = $item->getUnitPrice() * $quantity;

        $lineItem->setExtendedPrice($rowTotal - $item->getDiscountAmount());
        $lineItem->setLineItemId($item->getCode());

        return $lineItem;
    }


    /**
     * Create a {@see LineItemInterface}
     *
     * @return LineItemInterface
     */
    private function createLineItem()
    {
        return $this->factory->create();
    }
}
