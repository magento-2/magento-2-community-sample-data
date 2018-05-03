<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Calculation;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Registry;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;
use Vertex\Tax\Model\ItemCode;
use Vertex\Tax\Model\ItemType;

/**
 * Retrieves pre-calculated tax information for items and provides it during the tax-calculation process
 */
class VertexCalculator extends Calculation\UnitBaseCalculator
{
    const VERTEX_QUOTE_ITEM_ID_PREFIX = 'quote_item_id_';
    const VERTEX_LINE_ITEM_TAX_KEY = 'vertex_item_tax';
    const VERTEX_SHIPPING_LINE_ITEM_ID = 'shipping';
    const VERTEX_CALCULATION_ERROR = 'vertex_calculation_error';

    /** @var Registry */
    private $registry;

    /** @var DataObjectFactory */
    private $objectFactory;

    /** @var ItemType */
    private $itemType;

    /** @var ItemCode */
    private $itemCode;

    /**
     * @param TaxClassManagementInterface $taxClassService
     * @param TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory
     * @param AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory
     * @param AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory
     * @param Calculation $calculationTool
     * @param Config $config
     * @param int $storeId
     * @param Registry $registry
     * @param DataObjectFactory $objectFactory
     * @param ItemType $itemType
     * @param ItemCode $itemCode
     * @param DataObject|null $addressRateRequest
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        TaxClassManagementInterface $taxClassService,
        TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory,
        AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory,
        AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory,
        Calculation $calculationTool,
        Config $config,
        $storeId,
        Registry $registry,
        DataObjectFactory $objectFactory,
        ItemType $itemType,
        ItemCode $itemCode,
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

        $this->registry = $registry;
        $this->objectFactory = $objectFactory;
        $this->itemType = $itemType;
        $this->itemCode = $itemCode;
    }

    /**
     * {@inheritdoc}
     *
     * Protected method is part of override, $round required as part of override
     */
    protected function calculateWithTaxNotInPrice(QuoteDetailsItemInterface $item, $quantity, $round = true)
    {
        $itemTaxes = $this->getVertexItemTaxes();
        $itemTax = $itemTaxes !== null ? $this->calculateItemTax($item, $itemTaxes) : $this->objectFactory->create();

        $percent = $itemTax->hasTaxPercent() ? $itemTax->getTaxPercent() : 0;
        $rowTax = $itemTax->hasTaxAmount() ? $itemTax->getTaxAmount() : 0;

        // Determine whether or not to round the price
        $basePrice = $round ? $this->calculationTool->round($item->getUnitPrice()) : $item->getUnitPrice();

        $basePriceInclTax = $basePrice + \round($rowTax / $quantity, 2);

        $appliedTaxes = $this->prepareAppliedTaxes($item, $itemTax);

        return $this->taxDetailsItemDataObjectFactory->create()
            ->setCode($item->getCode())
            ->setType($item->getType())
            ->setRowTax($rowTax)
            ->setPrice($basePrice)
            ->setPriceInclTax($basePriceInclTax)
            ->setRowTotal($basePrice * $quantity)
            ->setRowTotalInclTax($basePriceInclTax * $quantity)
            ->setDiscountTaxCompensationAmount(0)
            ->setAssociatedItemCode($item->getAssociatedItemCode())
            ->setTaxPercent($percent)
            ->setAppliedTaxes($appliedTaxes);
    }

    /**
     * Retrieve Vertex Item Taxes
     *
     * @return mixed
     */
    public function getVertexItemTaxes()
    {
        return $this->registry->registry(self::VERTEX_LINE_ITEM_TAX_KEY);
    }

    /**
     * Retrieve an item's key for reference in the itemTaxes
     *
     * @param QuoteDetailsItemInterface $item
     * @return null|string
     */
    public function getItemKey(QuoteDetailsItemInterface $item)
    {
        return $this->registry->registry(self::VERTEX_QUOTE_ITEM_ID_PREFIX . $item->getCode());
    }

    /**
     * Retrieve an item's giftwrap key for reference in the itemTaxes
     *
     * @param QuoteDetailsItemInterface $item
     * @return null|string
     */
    public function getItemGwKey(QuoteDetailsItemInterface $item)
    {
        if (!$item->getAssociatedItemCode()) {
            return null;
        }

        $mainCode = $item->getAssociatedItemCode();
        $itemId = $this->registry->registry(self::VERTEX_QUOTE_ITEM_ID_PREFIX . $mainCode);
        return $this->registry->registry(self::VERTEX_QUOTE_ITEM_ID_PREFIX . 'gw' . $itemId);
    }

    /**
     * Retrieve the last registered calculation error.
     *
     * @return string|boolean Returns error message, or false if no error is set.
     */
    public function getError()
    {
        return $this->registry->registry(self::VERTEX_CALCULATION_ERROR) ?: false;
    }

    /**
     * Retrieve an object containing the relevant taxes for the QuoteDetailsItem
     *
     * @param QuoteDetailsItemInterface $item
     * @param array $itemTaxes
     * @return DataObject
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function calculateItemTax(
        QuoteDetailsItemInterface $item,
        $itemTaxes
    ) {
        /** @var DataObject $itemTax */
        $itemTax = $this->objectFactory->create();

        $key = $this->getItemKey($item);
        $gwKey = $this->getItemGwKey($item);

        $foundItem = isset($itemTaxes[$key]);
        $foundGwItem = isset($itemTaxes[$gwKey]);
        $type = $item->getType();

        if ($foundItem && $type === $this->itemType->product()) {
            // Product or Item-level Giftwrapping
            $itemTax = $itemTaxes[$key];
        } elseif ($foundGwItem && $type === $this->itemType->giftWrap()) {
            $itemTax = $itemTaxes[$gwKey];
        } elseif (isset($itemTaxes[self::VERTEX_SHIPPING_LINE_ITEM_ID]) && $type === $this->itemType->shipping()) {
            // Shipping
            $itemTax = $itemTaxes[self::VERTEX_SHIPPING_LINE_ITEM_ID];
        } elseif ($type === $this->itemType->orderPrintedCard() && isset($itemTaxes[$this->itemCode->printedCard()])) {
            // Printed Card
            $itemTax = $itemTaxes[$this->itemCode->printedCard()];
        } elseif ($type === $this->itemType->orderGiftWrap() && isset($itemTaxes[$this->itemCode->giftWrap()])) {
            // Order-level Giftwrapping
            $itemTax = $itemTaxes[$this->itemCode->giftWrap()];
        }

        /*
         * Magento_Tax runs through this function once for each currency.  We've calculated and cached the rate for the
         * base currency.  In this way, if the item's UnitPrice isn't equal to our UnitPrice from Vertex we know it's
         * a different currency and recalculate it.
         */
        if ($itemTax->hasUnitPrice() && $itemTax->getUnitPrice() !== $item->getUnitPrice()) {
            $itemTax->setTaxAmount($item->getQuantity() * $item->getUnitPrice() * $itemTax->getTaxRate());
        }

        return $itemTax;
    }

    /**
     * Get the applied tax rate title for the given rate type.
     *
     * @param string $rateType
     * @return string
     */
    private function getAppliedRateTitleByType($rateType)
    {
        switch ($rateType) {
            case $this->itemType->shipping():
                $title = 'Shipping';
                break;
            default:
                $title = 'Sales and Use';
                break;
        }

        return $title;
    }

    /**
     * Convert given Vertex tax details to applied tax information.
     *
     * @param QuoteDetailsItemInterface $item
     * @param DataObject $vertexItemTax Pre-calculated Vertex tax item response.
     * @return array
     */
    private function prepareAppliedTaxes(QuoteDetailsItemInterface $item, DataObject $vertexItemTax)
    {
        $appliedTaxes = [];

        if (!$vertexItemTax->getTaxAmount()) {
            return [];
        }

        $rateTitle = $this->getAppliedRateTitleByType($item->getType());
        $rateId = sha1($rateTitle);

        /** @var \Magento\Tax\Api\Data\AppliedTaxInterface $appliedTax */
        $appliedTax = $this->appliedTaxDataObjectFactory->create()
            ->setAmount($vertexItemTax->getTaxAmount())
            ->setPercent($vertexItemTax->getTaxPercent())
            ->setTaxRateKey($rateId)
            ->setRates([
                $rateId => $this->appliedTaxRateDataObjectFactory->create()
                    ->setPercent($vertexItemTax->getTaxPercent())
                    ->setCode($item->getCode())
                    ->setTitle($rateTitle),
            ]);

        $appliedTaxes[$item->getCode()] = $appliedTax;

        return $appliedTaxes;
    }
}
