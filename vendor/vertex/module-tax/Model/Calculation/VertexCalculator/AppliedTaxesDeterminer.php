<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Calculation\VertexCalculator;

use Magento\Framework\DataObject;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Vertex\Tax\Model\ItemType;

/**
 * Determine the applied taxes on tax-calculated items
 */
class AppliedTaxesDeterminer
{
    /** @var AppliedTaxInterfaceFactory  */
    private $appliedTaxDataObjectFactory;

    /** @var AppliedTaxRateInterfaceFactory */
    private $appliedTaxRateDataObjectFactory;

    /** @var ItemType */
    private $itemType;

    /**
     * @param AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory
     * @param AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory
     * @param ItemType $itemType
     */
    public function __construct(
        AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory,
        AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory,
        ItemType $itemType
    ) {
        $this->appliedTaxRateDataObjectFactory = $appliedTaxRateDataObjectFactory;
        $this->appliedTaxDataObjectFactory = $appliedTaxDataObjectFactory;
        $this->itemType = $itemType;
    }

    /**
     * Convert given Vertex tax details to applied tax information.
     *
     * @param QuoteDetailsItemInterface $item
     * @param DataObject $vertexItemTax Pre-calculated Vertex tax item response.
     * @return array
     */
    public function prepareAppliedTaxes(QuoteDetailsItemInterface $item, DataObject $vertexItemTax)
    {
        $appliedTaxes = [];

        if (!$vertexItemTax->getTaxAmount()) {
            return [];
        }

        $rateTitle = $this->getAppliedRateTitleByType($item->getType());
        $rateId = $item->getCode();
        $rateCode = $item->getType();

        /** @var \Magento\Tax\Api\Data\AppliedTaxInterface $appliedTax */
        $appliedTax = $this->appliedTaxDataObjectFactory->create()
            ->setAmount($vertexItemTax->getTaxAmount())
            ->setPercent($vertexItemTax->getTaxPercent())
            ->setTaxRateKey($rateCode)
            ->setRates(
                [
                    $rateId => $this->appliedTaxRateDataObjectFactory->create()
                        ->setPercent($vertexItemTax->getTaxPercent())
                        ->setCode($rateCode)
                        ->setTitle($rateTitle),
                ]
            );

        $appliedTaxes[$rateId] = $appliedTax;

        return $appliedTaxes;
    }

    /**
     * Get the applied tax rate title for the given rate type.
     *
     * We are not currently able to supply jurisdiction-level tax information. As a fallback, we can provide the
     * aggregate tax labels by rate type.
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
            case $this->itemType->giftWrap():
            case $this->itemType->orderGiftWrap():
            case $this->itemType->orderPrintedCard():
                $title = 'Gift Options';
                break;
            default:
                $title = 'Sales and Use';
                break;
        }

        return $title;
    }
}
