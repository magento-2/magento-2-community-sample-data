<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Framework\ObjectManagerInterface;
use Magento\GiftWrapping\Api\WrappingRepositoryInterface;
use Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote\Item;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;
use Vertex\Tax\Model\Calculation\VertexCalculator\ItemKeyManager;

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

    /** @var ItemKeyManager */
    private $itemKeyManager;

    /**
     * @param Seller $sellerFormatter
     * @param Customer $customerFormatter
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     * @param DeliveryTerm $deliveryTerm
     * @param TaxClassNameRepository $taxClassNameRepository
     * @param ItemKeyManager $itemKeyManager
     */
    public function __construct(
        Seller $sellerFormatter,
        Customer $customerFormatter,
        Config $config,
        ObjectManagerInterface $objectManager,
        DeliveryTerm $deliveryTerm,
        TaxClassNameRepository $taxClassNameRepository,
        ItemKeyManager $itemKeyManager
    ) {
        $this->customerFormatter = $customerFormatter;
        $this->objectManager = $objectManager;
        $this->sellerFormatter = $sellerFormatter;
        $this->config = $config;
        $this->deliveryTerm = $deliveryTerm;
        $this->taxClassNameRepository = $taxClassNameRepository;
        $this->itemKeyManager = $itemKeyManager;
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
     * @param QuoteAddress $taxAddress
     * @param Item\AbstractItem $taxAddressItem
     * @param int $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedLineItemData(QuoteAddress $taxAddress, $taxAddressItem, $customerGroupId = null)
    {
        $data = [];
        $storeId = $taxAddressItem->getQuote()->getStoreId();

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData($storeId);
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress, $customerGroupId);
        $data['Product'] = [
            '_' => substr($taxAddressItem->getData('sku'), 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED),
            'productClass' => $this->taxClassNameRepository->getById(
                $taxAddressItem->getProduct()
                    ->getData('tax_class_id')
            )
        ];

        $useOriginalPrice = $this->config->getApplyTaxOn($storeId) == Config::VALUE_APPLY_ON_ORIGINAL_ONLY;

        $data['Quantity'] = floatval($taxAddressItem->getQty());
        $data['UnitPrice'] = floatval($useOriginalPrice
            ? $taxAddressItem->getBaseOriginalPrice()
            : $taxAddressItem->getPrice());

        $rowTotal = floatval(
            $useOriginalPrice
                ? $taxAddressItem->getBaseOriginalPrice() * $taxAddressItem->getQty()
                : $taxAddressItem->getBaseRowTotal()
        );

        $data['ExtendedPrice'] = floatval($rowTotal - $taxAddressItem->getBaseDiscountAmount());
        $data['lineItemId'] = $this->itemKeyManager->createQuoteItemHash($taxAddressItem);
        $data['locationCode'] = $this->config->getLocationCode($storeId);

        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }

    /**
     * Create properly formatted Line Item data for an Order-level Printed Card
     *
     * @param QuoteAddress $taxAddress
     * @param int|null $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedOrderPrintCardData(QuoteAddress $taxAddress, $customerGroupId = null)
    {
        $data = [];
        $storeId = $taxAddress->getQuote()->getStoreId();

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress, $customerGroupId);
        $data['Product'] = [
            '_' => $this->config->getPrintedGiftcardCode($storeId),
            'productClass' => $this->taxClassNameRepository->getById(
                $this->config->getPrintedGiftcardClass($storeId)
            )
        ];

        $data['Quantity'] = 1;
        $data['UnitPrice'] = $taxAddress->getData('gw_card_base_price');

        if (empty($data['UnitPrice'])) {
            $printedCardBasePrice = $this->config->getPrintedCardPrice($storeId);
            $data['UnitPrice'] = $printedCardBasePrice;
        }
        if ($data['UnitPrice'] === null) {
            $data['UnitPrice'] = 0;
        }
        $data['ExtendedPrice'] = $data['UnitPrice'];
        $data['lineItemId'] = Giftwrapping::CODE_PRINTED_CARD;
        $data['locationCode'] = $this->config->getLocationCode($storeId);

        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }

    /**
     * Create properly formatted Line Item data for Order-level Giftwrapping
     *
     * @param QuoteAddress $taxAddress
     * @param int $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedOrderGiftWrapData(QuoteAddress $taxAddress, $customerGroupId = null)
    {
        $data = [];
        $storeId = $taxAddress->getQuote()->getStoreId();

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress, $customerGroupId);
        $data['Product'] = [
            '_' => $this->config->getGiftWrappingOrderCode($storeId),
            'productClass' => $this->taxClassNameRepository->getById(
                $this->config->getGiftWrappingOrderClass($storeId)
            )
        ];

        $data['Quantity'] = 1;

        $wrapping = $this->getWrappingRepository()->get(
            $taxAddress->getData('gw_id'),
            $storeId
        );
        $wrappingBaseAmount = $wrapping->getBasePrice();

        $data['UnitPrice'] = $wrappingBaseAmount;
        if ($data['UnitPrice'] === null) {
            $data['UnitPrice'] = 0;
        }
        $data['ExtendedPrice'] = $data['UnitPrice'];
        $data['lineItemId'] = Giftwrapping::CODE_QUOTE_GW;
        $data['locationCode'] = $this->config->getLocationCode($storeId);
        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }

    /**
     * Create properly formatted Line Item data for Item-level Giftwrapping
     *
     * @param QuoteAddress $taxAddress
     * @param Item\AbstractItem $item
     * @param int|null $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedItemGiftWrapData(QuoteAddress $taxAddress, $item, $customerGroupId = null)
    {
        $data = [];
        $storeId = $taxAddress->getQuote()->getStoreId();

        $data['Seller'] = $this->sellerFormatter->getFormattedSellerData();
        $data['Customer'] = $this->customerFormatter->getFormattedCustomerData($taxAddress, $customerGroupId);
        $data['Product'] = [
            '_' => $this->config->getGiftWrappingItemCodePrefix($storeId) . '-' . $item->getData('sku'),
            'productClass' => $this->taxClassNameRepository->getById(
                $this->config->getGiftWrappingItemClass($storeId)
            )
        ];

        if ($item->getData('gw_base_price')) {
            $wrappingBasePrice = $item->getData('gw_base_price');
        } else {
            $wrapping = $this->getWrappingRepository()->get($item->getData('gw_id'), $item->getData('store_id'));
            $wrappingBasePrice = $wrapping->getBasePrice();
        }

        $data['UnitPrice'] = $wrappingBasePrice;
        if ($data['UnitPrice'] === null) {
            $data['UnitPrice'] = 0;
        }

        $data['Quantity'] = $item->getQty();
        $data['ExtendedPrice'] = $data['Quantity'] * $data['UnitPrice'];
        $data['lineItemId'] = Giftwrapping ::CODE_ITEM_GW_PREFIX . '_' . $item->getId();
        $data = $this->deliveryTerm->addDeliveryTerm($data, $taxAddress);

        return $data;
    }
}
