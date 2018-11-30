<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ConfigurationValidator;

use Vertex\Data\AddressInterface;
use Vertex\Data\AddressInterfaceFactory;
use Vertex\Data\CustomerInterface;
use Vertex\Data\CustomerInterfaceFactory;
use Vertex\Data\LineItemInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Services\Quote\RequestInterface;
use Vertex\Services\Quote\RequestInterfaceFactory;
use Vertex\Tax\Model\Api\Data\SellerBuilder;
use Vertex\Tax\Model\DateTimeImmutableFactory;

/**
 * Tax calculation request verification utility.
 *
 * This class generates a test request for use in verifying the Vertex tax calculation service.
 */
class ValidSampleRequestBuilder
{
    const ADDRESS_CITY = 'King of Prussia';
    const ADDRESS_COUNTRY_ID = 'USA';
    const ADDRESS_POSTCODE = '19406';
    const ADDRESS_REGION = 'PA';
    const ADDRESS_STREET = '2301 Renaissance Blvd';
    const ITEM_PRICE = 10.00;
    const ITEM_QTY = 2;
    const ITEM_SKU = 'X-MOCK-ITEM';
    const ITEM_TAX_CLASS = 'Taxable Goods';

    /** @var AddressInterfaceFactory */
    private $addressFactory;

    /** @var CustomerInterfaceFactory */
    private $customerFactory;

    /** @var DateTimeImmutableFactory */
    private $dateTimeFactory;

    /** @var LineItemInterfaceFactory */
    private $lineItemFactory;

    /** @var RequestInterfaceFactory */
    private $requestFactory;

    /** @var string */
    private $scopeCode;

    /** @var string */
    private $scopeType;

    /** @var SellerBuilder */
    private $sellerFactory;

    /**
     * @param RequestInterfaceFactory $requestFactory
     * @param SellerBuilder $sellerFactory
     * @param AddressInterfaceFactory $addressFactory
     * @param LineItemInterfaceFactory $lineItemFactory
     * @param CustomerInterfaceFactory $customerFactory
     * @param DateTimeImmutableFactory $dateTimeFactory
     */
    public function __construct(
        RequestInterfaceFactory $requestFactory,
        SellerBuilder $sellerFactory,
        AddressInterfaceFactory $addressFactory,
        LineItemInterfaceFactory $lineItemFactory,
        CustomerInterfaceFactory $customerFactory,
        DateTimeImmutableFactory $dateTimeFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->sellerFactory = $sellerFactory;
        $this->addressFactory = $addressFactory;
        $this->lineItemFactory = $lineItemFactory;
        $this->customerFactory = $customerFactory;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * Generate a mock formatted tax service request.
     *
     * @return RequestInterface
     */
    public function build()
    {
        /** @var RequestInterface $request */
        $request = $this->requestFactory->create();
        $seller = $this->sellerFactory
            ->setScopeCode($this->scopeCode)
            ->setScopeType($this->scopeType)
            ->build();
        $request->setSeller($seller);
        $request->setCustomer($this->createCustomer());
        $request->setLineItems([$this->createLineItem()]);
        $request->setTransactionType(RequestInterface::TRANSACTION_TYPE_SALE);
        $request->setDocumentDate($this->dateTimeFactory->create());

        return $request;
    }

    /**
     * Set the store code
     *
     * @param string|null $scopeCode
     * @return ValidSampleRequestBuilder
     */
    public function setScopeCode($scopeCode)
    {
        $this->scopeCode = $scopeCode;
        return $this;
    }

    /**
     * Set the scope type
     *
     * @param string|null $scopeType
     * @return ValidSampleRequestBuilder
     */
    public function setScopeType($scopeType)
    {
        $this->scopeType = $scopeType;
        return $this;
    }

    /**
     * Prepare the given address with mock data.
     *
     * @return AddressInterface
     */
    private function createAddress()
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->create();
        $address->setStreetAddress([static::ADDRESS_STREET]);
        $address->setCity(static::ADDRESS_CITY);
        $address->setMainDivision(static::ADDRESS_REGION);
        $address->setPostalCode(static::ADDRESS_POSTCODE);
        $address->setCountry(static::ADDRESS_COUNTRY_ID);
        return $address;
    }

    /**
     * Prepare a Customer Record
     *
     * @return CustomerInterface
     */
    private function createCustomer()
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->create();
        $customer->setDestination($this->createAddress());

        return $customer;
    }

    /**
     * Generate a mock quote item.
     *
     * @return LineItemInterface
     */
    private function createLineItem()
    {
        /** @var LineItemInterface $lineItem */
        $lineItem = $this->lineItemFactory->create();
        $lineItem->setProductCode(static::ITEM_SKU);
        $lineItem->setProductClass(static::ITEM_TAX_CLASS);
        $lineItem->setQuantity(static::ITEM_QTY);
        $lineItem->setUnitPrice(static::ITEM_PRICE);
        $lineItem->setExtendedPrice(static::ITEM_PRICE * static::ITEM_QTY);

        return $lineItem;
    }
}
