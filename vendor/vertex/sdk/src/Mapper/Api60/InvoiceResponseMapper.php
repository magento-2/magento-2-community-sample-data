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
use Vertex\Mapper\InvoiceResponseMapperInterface;
use Vertex\Mapper\SellerMapperInterface;
use Vertex\Services\Invoice\Response;
use Vertex\Services\Invoice\ResponseInterface;

/**
 * API Level 60 implementation of {@see InvoiceResponseMapperInterface}
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InvoiceResponseMapper implements InvoiceResponseMapperInterface
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
     * @param SellerMapperInterface|null $sellerMapper
     * @param LineItemMapperInterface|null $lineItemMapper
     */
    public function __construct(
        MapperUtilities $utilities = null,
        CustomerMapperInterface $customerMapper = null,
        SellerMapperInterface $sellerMapper = null,
        LineItemMapperInterface $lineItemMapper = null
    ) {
        $this->utilities = $utilities ?: new MapperUtilities();
        $this->customerMapper = $customerMapper ?: new CustomerMapper();
        $this->sellerMapper = $sellerMapper ?: new SellerMapper();
        $this->lineItemMapper = $lineItemMapper ?: new LineItemMapper();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function build(\stdClass $map)
    {
        $object = new Response();
        $map = $map->InvoiceResponse;

        // Attributes
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

        // Tags
        if (isset($map->Customer)) {
            $object->setCustomer($this->customerMapper->build($map->Customer));
        }
        if (isset($map->Seller)) {
            $object->setSeller($this->sellerMapper->build($map->Seller));
        }
        $rawLineItems = isset($map->LineItem) ? $map->LineItem : [];
        if ($rawLineItems instanceof \stdClass) {
            $rawLineItems = [$rawLineItems];
        }
        $lineItems = [];
        foreach ($rawLineItems as $rawLineItem) {
            $lineItems[] = $this->lineItemMapper->build($rawLineItem);
        }
        $object->setLineItems($lineItems);
        if (isset($map->SubTotal)) {
            $object->setSubtotal($map->SubTotal instanceof \stdClass ? $map->SubTotal->_ : $map->SubTotal);
        }
        if (isset($map->Total)) {
            $object->setTotal($map->Total instanceof \stdClass ? $map->Total->_ : $map->Total);
        }
        if (isset($map->TotalTax)) {
            $object->setTotalTax($map->TotalTax instanceof \stdClass ? $map->TotalTax->_ : $map->TotalTax);
        }

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function map(ResponseInterface $object)
    {
        $map = new \stdClass();

        // Attributes
        $map = $this->addDeliveryTermToMap($object, $map);
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
                ResponseInterface::TRANSACTION_TYPE_LEASE,
                ResponseInterface::TRANSACTION_TYPE_RENTAL,
                ResponseInterface::TRANSACTION_TYPE_SALE
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
        $map->LineItems = $lineItems;

        $map = $this->utilities->addToMapWithDecimalValidation($map, $object->getSubtotal(), 'SubTotal');
        $map = $this->utilities->addToMapWithDecimalValidation($map, $object->getTotal(), 'Total');
        $map = $this->utilities->addToMapWithDecimalValidation(
            $map,
            $object->getTotal(),
            'TotalTax',
            PHP_INT_MIN,
            PHP_INT_MAX,
            true,
            'Total Tax'
        );

        $response = new \stdClass();
        $response->InvoiceResponse = $map;
        return $response;
    }

    /**
     * Add Customer to map if set
     *
     * @param \stdClass $map
     * @param ResponseInterface $object
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addCustomerToMap(\stdClass $map, ResponseInterface $object)
    {
        if ($object->getCustomer() !== null) {
            $map->Customer = $this->customerMapper->map($object->getCustomer());
        }
        return $map;
    }

    /**
     * Add the Delivery Term to the map
     *
     * @param ResponseInterface $object
     * @param \stdClass $map
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addDeliveryTermToMap(ResponseInterface $object, \stdClass $map)
    {
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
        return $map;
    }

    /**
     * Add Seller to map if set
     *
     * @param \stdClass $map
     * @param ResponseInterface $object
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addSellerToMap(\stdClass $map, ResponseInterface $object)
    {
        if ($object->getSeller() !== null) {
            $map->Seller = $this->sellerMapper->map($object->getSeller());
        }
        return $map;
    }
}
