<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Framework\ObjectManagerInterface;
use Magento\GiftWrapping\Api\WrappingRepositoryInterface;
use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Line Item data formatter for Vertex API Calls
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LineItem
{
    /** @var Customer */
    private $customerFormatter;

    /** @var Seller */
    private $sellerFormatter;

    /** @var Config */
    private $config;

    /** @var WrappingRepositoryInterface */
    private $wrappingRepository;

    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var DeliveryTerm */
    private $deliveryTerm;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param Seller $sellerFormatter
     * @param Customer $customerFormatter
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     * @param DeliveryTerm $deliveryTerm
     * @param TaxClassNameRepository $taxClassNameRepository
     */
    public function __construct(
        Seller $sellerFormatter,
        Customer $customerFormatter,
        Config $config,
        ObjectManagerInterface $objectManager,
        DeliveryTerm $deliveryTerm,
        TaxClassNameRepository $taxClassNameRepository
    ) {
        $this->customerFormatter = $customerFormatter;
        $this->objectManager = $objectManager;
        $this->sellerFormatter = $sellerFormatter;
        $this->config = $config;
        $this->deliveryTerm = $deliveryTerm;
        $this->taxClassNameRepository = $taxClassNameRepository;
    }

    /**
     * Retrieve the Giftwrapping Repository, if available
     *
     * @return WrappingRepositoryInterface|mixed
     */
    private function getWrappingRepository()
    {
        if ($this->wrappingRepository === null) {
            // ObjectManager required for Commerce features
            $this->wrappingRepository = $this->objectManager->get(WrappingRepositoryInterface::class);
        }
        return $this->wrappingRepository;
    }

    /**
     * Create properly formatted Line Item data for a Vertex API Call
     *
     * @param Address $taxAddress
     * @param Item\AbstractItem $taxAddressItem
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedLineItemData(Address $taxAddress, $taxAddressItem)
    {
        $data = [];

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress);
        $data['Product'] = [
            '_' => substr($taxAddressItem->getData('sku'), 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED),
            'productClass' => $this->taxClassNameRepository->getById(
                $taxAddressItem->getProduct()
                    ->getData('tax_class_id')
            )
        ];

        $storeId = $taxAddressItem->getStore() ? $taxAddressItem->getStore()->getId() : null;
        $useOriginalPrice = $this->config->getApplyTaxOn($storeId) == Config::VALUE_APPLY_ON_ORIGINAL_ONLY;

        $data['Quantity'] = $taxAddressItem->getQty();
        $data['UnitPrice'] = $useOriginalPrice ? $taxAddressItem->getBaseOriginalPrice() : $taxAddressItem->getPrice();

        $rowTotal = $useOriginalPrice
            ? $taxAddressItem->getBaseOriginalPrice() * $taxAddressItem->getQty()
            : $taxAddressItem->getBaseRowTotal();

        $data['ExtendedPrice'] = $rowTotal - $taxAddressItem->getBaseDiscountAmount();

        $data['lineItemId'] = method_exists($taxAddressItem, 'getItemId') && $taxAddressItem->getItemId()
            ? $taxAddressItem->getItemId()
            : $taxAddressItem->getId();

        $data['locationCode'] = $this->config->getLocationCode();

        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }

    /**
     * Create properly formatted Line Item data for an Order-level Printed Card
     *
     * @param Address $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedOrderPrintCardData(Address $taxAddress)
    {
        $data = [];

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress);
        $data['Product'] = [
            '_' => $this->config->getPrintedGiftcardCode(),
            'productClass' => $this->taxClassNameRepository->getById(
                $this->config->getPrintedGiftcardClass()
            )
        ];

        $data['Quantity'] = 1;
        $data['UnitPrice'] = $taxAddress->getData('gw_card_base_price');

        if (empty($data['UnitPrice'])) {
            $printedCardBasePrice = $this->config->getPrintedCardPrice($taxAddress->getData('store_id'));
            $data['UnitPrice'] = $printedCardBasePrice;
        }
        $data['ExtendedPrice'] = $data['UnitPrice'];
        $data['lineItemId'] = Giftwrapping::CODE_PRINTED_CARD;
        $data['locationCode'] = $this->config->getLocationCode();

        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }

    /**
     * Create properly formatted Line Item data for Order-level Giftwrapping
     *
     * @param Address $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedOrderGiftWrapData(Address $taxAddress)
    {
        $data = [];

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress);
        $data['Product'] = [
            '_' => $this->config->getGiftWrappingOrderCode(),
            'productClass' => $this->taxClassNameRepository->getById(
                $this->config->getGiftWrappingOrderClass()
            )
        ];

        $data['Quantity'] = 1;

        $wrapping = $this->getWrappingRepository()->get(
            $taxAddress->getData('gw_id'),
            $taxAddress->getData('store_id')
        );
        $wrappingBaseAmount = $wrapping->getBasePrice();

        $data['UnitPrice'] = $wrappingBaseAmount;
        $data['ExtendedPrice'] = $data['UnitPrice'];
        $data['lineItemId'] = Giftwrapping::CODE_QUOTE_GW;
        $data['locationCode'] = $this->config->getLocationCode();
        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }

    /**
     * Create properly formatted Line Item data for Item-level Giftwrapping
     *
     * @param Address $taxAddress
     * @param Item\AbstractItem $item
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedItemGiftWrapData(Address $taxAddress, $item)
    {
        $data = [];

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress);
        $data['Product'] = [
            '_' => $this->config->getGiftWrappingItemCodePrefix() . '-' . $item->getData('sku'),
            'productClass' => $this->taxClassNameRepository->getById(
                $this->config->getGiftWrappingItemClass()
            )
        ];

        if ($item->getData('gw_base_price')) {
            $wrappingBasePrice = $item->getData('gw_base_price');
        } else {
            $wrapping = $this->getWrappingRepository()->get($item->getData('gw_id'), $item->getData('store_id'));
            $wrappingBasePrice = $wrapping->getBasePrice();
        }

        $data['UnitPrice'] = $wrappingBasePrice;

        $data['Quantity'] = $item->getQty();
        $data['ExtendedPrice'] = $data['Quantity'] * $data['UnitPrice'];
        $data['lineItemId'] = Giftwrapping ::CODE_ITEM_GW_PREFIX . '_' . $item->getId();
        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }
}
