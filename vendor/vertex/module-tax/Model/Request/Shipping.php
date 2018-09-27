<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Quote\Api\Data\AddressInterface;
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
     * @param AddressInterface $taxAddress
     * @param int|null $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedShippingLineItemData(AddressInterface $taxAddress, $customerGroupId = null)
    {
        $data = [];
        $storeId = $taxAddress->getQuote()->getStoreId();

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData($storeId);
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress, $customerGroupId);
        $data['Product'] = [
            '_' => substr($taxAddress->getShippingMethod(), 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED),
            'productClass' => substr(
                $this->taxClassNameRepository->getById(
                    $this->config->getShippingTaxClassId($storeId)
                ),
                0,
                Config::MAX_CHAR_PRODUCT_CODE_ALLOWED
            )
        ];

        $data['Quantity'] = 1;
        $rate = $taxAddress->getShippingRateByCode($taxAddress->getShippingMethod());

        if ($taxAddress->getShippingMethod() && !$rate) {
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

        $data['UnitPrice'] = $unitPrice;
        $data['ExtendedPrice'] = $extendedPrice;
        $data['lineItemId'] = static::LINE_ITEM_ID;
        $data['locationCode'] = $this->config->getLocationCode($storeId);

        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }
}
