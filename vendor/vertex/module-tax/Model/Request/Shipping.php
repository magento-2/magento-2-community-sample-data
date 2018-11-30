<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Quote\Api\Data\AddressInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Shipping data formatter for Vertex API Calls
 */
class Shipping
{
    const LINE_ITEM_ID = VertexCalculator::VERTEX_SHIPPING_LINE_ITEM_ID;

    /** @var Config */
    private $config;

    /** @var LineItemInterfaceFactory */
    private $lineItemFactory;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param Config $config
     * @param TaxClassNameRepository $taxClassNameRepository
     * @param LineItemInterfaceFactory $lineItemFactory
     */
    public function __construct(
        Config $config,
        TaxClassNameRepository $taxClassNameRepository,
        LineItemInterfaceFactory $lineItemFactory
    ) {
        $this->config = $config;
        $this->taxClassNameRepository = $taxClassNameRepository;
        $this->lineItemFactory = $lineItemFactory;
    }

    /**
     * Create properly formatted Line Item data for the Order Shipping
     *
     * @param AddressInterface $taxAddress
     * @param string|null $scopeCode
     * @return LineItemInterface
     */
    public function getFormattedShippingLineItemData(AddressInterface $taxAddress, $scopeCode = null)
    {
        /** @var LineItemInterface $item */
        $item = $this->lineItemFactory->create();

        $item->setProductCode(substr($taxAddress->getShippingMethod(), 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED));
        $item->setProductClass(
            substr(
                $this->taxClassNameRepository->getById(
                    $this->config->getShippingTaxClassId($scopeCode)
                ),
                0,
                Config::MAX_CHAR_PRODUCT_CODE_ALLOWED
            )
        );

        $item->setQuantity(1);
        $rate = $taxAddress->getShippingRateByCode($taxAddress->getShippingMethod());

        if (!$rate && $taxAddress->getShippingMethod()) {
            $taxAddress->setCollectShippingRates(true)->collectShippingRates();
        }

        foreach ($taxAddress->getAllShippingRates() as $rateCandidate) {
            if ($rateCandidate->getCode() === $taxAddress->getShippingMethod()) {
                $rate = $rateCandidate;

                break;
            }
        }

        $unitPrice = $rate ? $rate->getPrice() : 0;
        $extendedPrice = $unitPrice ? $unitPrice - $taxAddress->getBaseShippingDiscountAmount() : 0;

        $item->setUnitPrice($unitPrice);
        $item->setExtendedPrice($extendedPrice);
        $item->setLineItemId(static::LINE_ITEM_ID);

        return $item;
    }
}
