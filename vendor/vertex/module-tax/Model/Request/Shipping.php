<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Quote\Model\Quote\Address;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Shipping data formatter for Vertex API Calls
 */
class Shipping
{
    const LINE_ITEM_ID = VertexCalculator::VERTEX_SHIPPING_LINE_ITEM_ID;

    /** @var Customer */
    private $customerFormatter;

    /** @var Seller */
    private $sellerFormatter;

    /** @var Config */
    private $config;

    /** @var DeliveryTerm */
    private $deliveryTerm;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param Seller $sellerFormatter
     * @param Customer $customerFormatter
     * @param Config $config
     * @param DeliveryTerm $deliveryTerm
     * @param TaxClassNameRepository $taxClassNameRepository
     */
    public function __construct(
        Seller $sellerFormatter,
        Customer $customerFormatter,
        Config $config,
        DeliveryTerm $deliveryTerm,
        TaxClassNameRepository $taxClassNameRepository
    ) {
        $this->customerFormatter = $customerFormatter;
        $this->sellerFormatter = $sellerFormatter;
        $this->config = $config;
        $this->deliveryTerm = $deliveryTerm;
        $this->taxClassNameRepository = $taxClassNameRepository;
    }

    /**
     * Create properly formatted Line Item data for the Order Shipping
     *
     * @param Address $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedShippingLineItemData(Address $taxAddress)
    {
        $data = [];

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress);
        $data['Product'] = [
            '_' => substr($taxAddress->getShippingMethod(), 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED),
            'productClass' => substr(
                $this->taxClassNameRepository->getById(
                    $this->config->getShippingTaxClassId()
                ),
                0,
                Config::MAX_CHAR_PRODUCT_CODE_ALLOWED
            )
        ];

        $data['Quantity'] = 1;

        if (!$taxAddress->getShippingMethod()) {
            $taxAddress->setCollectShippingRates(true)->collectShippingRates();
        }

        $rate = null;

        foreach ($taxAddress->getAllShippingRates() as $rateCandidate) {
            if ($rateCandidate->getCode() === $taxAddress->getShippingMethod()) {
                $rate = $rateCandidate;

                break;
            }
        }

        $price = $rate ? $rate->getPrice() : 0;
        $price -= $rate ? $taxAddress->getBaseShippingDiscountAmount() : 0;

        $data['UnitPrice'] = $price;
        $data['ExtendedPrice'] = $data['UnitPrice'];
        $data['lineItemId'] = static::LINE_ITEM_ID;
        $data['locationCode'] = $this->config->getLocationCode();

        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }
}
