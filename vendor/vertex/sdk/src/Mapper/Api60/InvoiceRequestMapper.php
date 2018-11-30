<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\DeliveryTerm;
use Vertex\Mapper\CustomerMapperInterface;
use Vertex\Mapper\LineItemMapperInterface;
use Vertex\Mapper\MapperUtilities;
use Vertex\Mapper\InvoiceRequestMapperInterface;
use Vertex\Mapper\SellerMapperInterface;
use Vertex\Services\Invoice\Request;
use Vertex\Services\Invoice\RequestInterface;

/**
 * API Level 60 implementation of {@see InvoiceRequestMapperInterface}
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InvoiceRequestMapper implements InvoiceRequestMapperInterface
{
    /**
     * Maximum characters allowed for the documentNumber attribute
     */
    const DOCUMENT_NUMBER_MAX = 40;

    /**
     * Minimum characters allowed for the documentNumber attribute
     */
    const DOCUMENT_NUMBER_MIN = 1;

    /**
     * Maximum characters allowed for the locationCode attribute
     */
    const LOCATION_CODE_MAX = 20;

    /**
     * Minimum characters allowed for the locationCode attribute
     */
    const LOCATION_CODE_MIN = 1;

    /**
     * Maximum characters allowed for the transactionId attribute
     */
    const TRANSACTION_ID_MAX = 40;

    /**
     * Minimum characters allowed for the transactionId attribute
     */
    const TRANSACTION_ID_MIN = 1;

    /** @var CustomerMapperInterface */
    private $customerMapper;

    /** @var LineItemMapperInterface */
    private $lineItemMapper;

    /** @var SellerMapperInterface */
    private $sellerMapper;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param MapperUtilities|null $utilities
     * @param CustomerMapperInterface|null $customerMapper
     * @param LineItemMapperInterface|null $lineItemMapper
     * @param SellerMapperInterface|null $sellerMapper
     */
    public function __construct(
        MapperUtilities $utilities = null,
        CustomerMapperInterface $customerMapper = null,
        LineItemMapperInterface $lineItemMapper = null,
        SellerMapperInterface $sellerMapper = null
    ) {
        $this->utilities = $utilities ?: new MapperUtilities();
        $this->customerMapper = $customerMapper ?: new CustomerMapper();
        $this->lineItemMapper = $lineItemMapper ?: new LineItemMapper();
        $this->sellerMapper = $sellerMapper ?: new SellerMapper();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function build(\stdClass $map)
    {
        $object = new Request();
        $map = $map->InvoiceRequest;
        if (isset($map->deliveryTerm)) {
            $object->setDeliveryTerm($map->deliveryTerm);
        }
        if (isset($map->documentDate)) {
            $object->setDocumentDate(new \DateTime($map->documentDate));
        }
        if (isset($map->documentNumber)) {
            $object->setDocumentNumber($map->documentNumber);
        }
        if (isset($map->locationCode)) {
            $object->setLocationCode($map->locationCode);
        }
        if (isset($map->transactionId)) {
            $object->setTransactionId($map->transactionId);
        }
        if (isset($map->transactionType)) {
            $object->setTransactionType($map->transactionType);
        }
        if (isset($map->Seller)) {
            $object->setSeller($this->sellerMapper->build($map->Seller));
        }
        if (isset($map->Customer)) {
            $object->setCustomer($this->customerMapper->build($map->Customer));
        }
        $rawLineItems = $map->LineItems;
        if ($rawLineItems instanceof \stdClass) {
            $rawLineItems = [$rawLineItems];
        }
        $lineItems = [];
        foreach ($rawLineItems as $rawLineItem) {
            $lineItems[] = $this->lineItemMapper->build($rawLineItem);
        }
        $object->setLineItems($lineItems);

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function map(RequestInterface $object)
    {
        $map = new \stdClass();

        // Attributes
        $map = $this->utilities->addToMapWithEnumerationValidation(
            $map,
            $object->getDeliveryTerm(),
            'deliveryTerm',
            [
                DeliveryTerm::CFR,
                DeliveryTerm::CIF,
                DeliveryTerm::CIP,
                DeliveryTerm::CPT,
                DeliveryTerm::CUS,
                DeliveryTerm::DAF,
                DeliveryTerm::DAP,
                DeliveryTerm::DAT,
                DeliveryTerm::DDP,
                DeliveryTerm::DDU,
                DeliveryTerm::DEQ,
                DeliveryTerm::DES,
                DeliveryTerm::EXW,
                DeliveryTerm::FAS,
                DeliveryTerm::FCA,
                DeliveryTerm::FOB,
                DeliveryTerm::SUP
            ],
            true,
            'Delivery Term'
        );
        $map = $this->utilities->addToMapWithDateValidation(
            $map,
            $object->getDocumentDate(),
            'documentDate',
            true,
            'Document Date'
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getDocumentNumber(),
            'documentNumber',
            static::DOCUMENT_NUMBER_MIN,
            static::DOCUMENT_NUMBER_MAX,
            true,
            'Document Number'
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getLocationCode(),
            'locationCode',
            static::LOCATION_CODE_MIN,
            static::LOCATION_CODE_MAX,
            true,
            'Location Code'
        );

        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getTransactionId(),
            'transactionId',
            static::TRANSACTION_ID_MIN,
            static::TRANSACTION_ID_MAX,
            true,
            'Transaction ID'
        );
        $map = $this->utilities->addToMapWithEnumerationValidation(
            $map,
            $object->getTransactionType(),
            'transactionType',
            [
                RequestInterface::TRANSACTION_TYPE_LEASE,
                RequestInterface::TRANSACTION_TYPE_RENTAL,
                RequestInterface::TRANSACTION_TYPE_SALE
            ],
            true,
            'Transaction Type'
        );

        // Child Tags
        $map = $this->addSellerToMap($map, $object);
        $map = $this->addCustomerToMap($map, $object);
        $lineItems = [];
        foreach ($object->getLineItems() as $lineItem) {
            $lineItems[] = $this->lineItemMapper->map($lineItem);
        }
        $map->LineItem = $lineItems;

        $request = new \stdClass();
        $request->InvoiceRequest = $map;
        return $request;
    }

    /**
     * Add Customer to map if set
     *
     * @param \stdClass $map
     * @param RequestInterface $object
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addCustomerToMap(\stdClass $map, RequestInterface $object)
    {
        if ($object->getCustomer() !== null) {
            $map->Customer = $this->customerMapper->map($object->getCustomer());
        }
        return $map;
    }

    /**
     * Add Seller to map if set
     *
     * @param \stdClass $map
     * @param RequestInterface $object
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addSellerToMap(\stdClass $map, RequestInterface $object)
    {
        if ($object->getSeller() !== null) {
            $map->Seller = $this->sellerMapper->map($object->getSeller());
        }
        return $map;
    }
}
