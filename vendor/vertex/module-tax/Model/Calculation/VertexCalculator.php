<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Calculation;

use Magento\Framework\DataObject;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;
use Vertex\Tax\Model\Calculation\VertexCalculator\AppliedTaxesDeterminer;
use Vertex\Tax\Model\Calculation\VertexCalculator\ItemCalculator;

/**
 * Retrieves pre-calculated tax information for items and provides it during the tax-calculation process
 */
class VertexCalculator extends Calculation\UnitBaseCalculator
{
    const VERTEX_CALCULATION_ERROR = 'vertex_calculation_error';
    const VERTEX_QUOTE_ITEM_ID_PREFIX = 'quote_item_id_';
    const VERTEX_LINE_ITEM_TAX_KEY = 'vertex_item_tax';
    const VERTEX_SHIPPING_LINE_ITEM_ID = 'shipping';

    /** @var ItemCalculator */
    private $itemCalculator;

    /** @var AppliedTaxesDeterminer */
    private $appliedTaxesDeterminer;

    /**
     * @param TaxClassManagementInterface $taxClassService
     * @param TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory
     * @param AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory
     * @param AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory
     * @param Calculation $calculationTool
     * @param Config $config
     * @param int $storeId
     * @param ItemCalculator $itemCalculator
     * @param AppliedTaxesDeterminer $appliedTaxesDeterminer
     * @param DataObject|null $addressRateRequest
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList) Necessary for parent class
     */
    public function __construct(
        TaxClassManagementInterface $taxClassService,
        TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory,
        AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory,
        AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory,
        Calculation $calculationTool,
        Config $config,
        $storeId,
        ItemCalculator $itemCalculator,
        AppliedTaxesDeterminer $appliedTaxesDeterminer,
        DataObject $addressRateRequest = null
    ) {
        parent::__construct(
            $taxClassService,
            $taxDetailsItemDataObjectFactory,
            $appliedTaxDataObjectFactory,
            $appliedTaxRateDataObjectFactory,
            $calculationTool,
            $config,
            $storeId,
            $addressRateRequest
        );

        $this->itemCalculator = $itemCalculator;
        $this->appliedTaxesDeterminer = $appliedTaxesDeterminer;
    }

    /**
     * {@inheritdoc}
     *
     * Protected method is part of override, $round required as part of override
     */
    protected function calculateWithTaxNotInPrice(QuoteDetailsItemInterface $item, $quantity, $round = true)
    {
        $itemTaxes = $this->itemCalculator->getVertexItemTaxes();

        /** @var DataObject $itemTax */
        $itemTax = $itemTaxes !== null
            ? $this->itemCalculator->calculateItemTax($item, $itemTaxes, $round)
            : $this->itemCalculator->getEmptyItemTax();

        $percent = $itemTax->hasTaxPercent() ? $itemTax->getTaxPercent() : 0;
        $rowTax = $itemTax->hasTaxAmount() ? $itemTax->getTaxAmount() : 0;

        // Determine whether or not to round the price
        $basePrice = $round ? $this->calculationTool->round($item->getUnitPrice()) : $item->getUnitPrice();

        $perItemTax = $rowTax / $quantity;
        $basePriceInclTax = $basePrice + ($round ? $this->calculationTool->round($perItemTax) : $perItemTax);

        $appliedTaxes = $this->appliedTaxesDeterminer->prepareAppliedTaxes($item, $itemTax);

        $rowTotal = $basePrice * $quantity;

        return $this->taxDetailsItemDataObjectFactory->create()
            ->setCode($item->getCode())
            ->setType($item->getType())
            ->setRowTax($rowTax)
            ->setPrice($basePrice)
            ->setPriceInclTax($basePriceInclTax)
            ->setRowTotal($rowTotal)
            ->setRowTotalInclTax($rowTotal + $rowTax)
            ->setDiscountTaxCompensationAmount(0)
            ->setAssociatedItemCode($item->getAssociatedItemCode())
            ->setTaxPercent($percent)
            ->setAppliedTaxes($appliedTaxes);
    }
}
