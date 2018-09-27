<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ConfigurationValidator;

use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Vertex\Tax\Model\Request\Type\QuotationRequest;

/**
 * Tax calculation request verification utility.
 *
 * This class generates a test request for use in verifying the Vertex tax calculation service.
 */
class ValidSampleRequestFactory
{
    const ITEM_ID = null;
    const ITEM_SKU = 'X-MOCK-ITEM';
    const ITEM_QTY = 2;
    const ITEM_NAME = 'Mock Quote Item';
    const ITEM_PRICE = 10.00;
    const ITEM_TYPE = 'simple';
    const ITEM_QUOTE_ID = null;
    const ITEM_PRODUCT_ID = 9999999999;
    const ADDRESS_STREET = '2301 Renaissance Blvd';
    const ADDRESS_CITY = 'King of Prussia';
    const ADDRESS_REGION = 'PA';
    const ADDRESS_REGION_ID = 51;
    const ADDRESS_POSTCODE = '19406';
    const ADDRESS_COUNTRY_ID = 'US';
    const PRODUCT_TAX_CLASS_ID = 2;

    /** @var ProductInterfaceFactory */
    private $productFactory;

    /** @var QuotationRequest */
    private $quotationRequestFormatter;

    /** @var QuoteFactory */
    private $quoteFactory;

    /** @var ItemFactory */
    private $quoteItemFactory;

    /** @var AddressInterfaceFactory */
    private $taxAddressFactory;

    /**
     * @param QuotationRequest $quotationRequestFormatter
     * @param AddressInterfaceFactory $taxAddressFactory
     * @param QuoteFactory $quoteFactory
     * @param ItemFactory $quoteItemFactory
     * @param ProductInterfaceFactory $productFactory
     */
    public function __construct(
        QuotationRequest $quotationRequestFormatter,
        AddressInterfaceFactory $taxAddressFactory,
        QuoteFactory $quoteFactory,
        ItemFactory $quoteItemFactory,
        ProductInterfaceFactory $productFactory
    ) {
        $this->quotationRequestFormatter = $quotationRequestFormatter;
        $this->taxAddressFactory = $taxAddressFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * Generate a mock formatted tax service request.
     *
     * @param array $data Optional user-data to seed the request.
     * @return array
     */
    public function create(array $data = [])
    {
        /** @var AddressInterface $taxAddress */
        $taxAddress = $this->taxAddressFactory->create($data);

        $this->populateTaxAddress($taxAddress);

        return $this->quotationRequestFormatter->getFormattedRequestData($taxAddress);
    }

    /**
     * Prepare the given address with mock data.
     *
     * @param AddressInterface $address
     * @return void
     */
    private function populateTaxAddress(AddressInterface $address)
    {
        $address->setQuote($this->prepareQuote($address)) // Implementor defines method
            ->setAddressType('shipping') // Implementor provides method
            ->setStreet($address->getStreet() ?: self::ADDRESS_STREET)
            ->setCity($address->getCity() ?: self::ADDRESS_CITY)
            ->setRegionId($address->getRegionId() ?: self::ADDRESS_REGION_ID)
            ->setRegion($address->getRegion() ?: self::ADDRESS_REGION)
            ->setCountryId($address->getCountryId() ?: self::ADDRESS_COUNTRY_ID)
            ->setPostcode($address->getPostcode() ?: self::ADDRESS_POSTCODE);
    }

    /**
     * Generate a mock quote with item data.
     *
     * @param AddressInterface $address
     * @return \Magento\Quote\Model\Quote
     */
    private function prepareQuote(AddressInterface $address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteFactory->create();

        $quote->addItem($this->prepareQuoteItem())
            ->setBillingAddress($address)
            ->setShippingAddress($address);

        return $quote;
    }

    /**
     * Generate a mock quote item.
     *
     * @return \Magento\Quote\Api\Data\CartItemInterface
     */
    private function prepareQuoteItem()
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->productFactory->create([
            'data' => [
                'tax_class_id' => self::PRODUCT_TAX_CLASS_ID,
            ],
        ]);

        /** @var \Magento\Quote\Api\Data\CartItemInterface $item */
        $item = $this->quoteItemFactory->create([
            'data' => [
                'item_id' => self::ITEM_ID,
                'sku' => self::ITEM_SKU,
                'qty' => self::ITEM_QTY,
                'name' => self::ITEM_NAME,
                'price' => self::ITEM_PRICE,
                'product_type' => self::ITEM_TYPE,
                'quote_id' => self::ITEM_QUOTE_ID,
            ],
        ]);

        $item->setData('product', $product); // Implementor provides method
        $item->setProductId(self::ITEM_PRODUCT_ID); // Implementor provides method
        $item->setTaxClassId(self::PRODUCT_TAX_CLASS_ID); // Implementor provides method
        $item->setBaseOriginalPrice(self::ITEM_PRICE); // Implementor provides method
        $item->setBaseRowTotal(self::ITEM_PRICE * self::ITEM_QTY); // Implementor provides method

        return $item;
    }
}
