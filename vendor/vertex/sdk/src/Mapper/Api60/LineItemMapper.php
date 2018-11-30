<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\DeliveryTerm;
use Vertex\Data\LineItem;
use Vertex\Data\LineItemInterface;
use Vertex\Mapper\CustomerMapperInterface;
use Vertex\Mapper\LineItemMapperInterface;
use Vertex\Mapper\SellerMapperInterface;
use Vertex\Mapper\MapperUtilities;
use Vertex\Mapper\TaxMapperInterface;

/**
 * API Level 60 implementation of {@see LineItemMapperInterface}
 */
class LineItemMapper implements LineItemMapperInterface
{
    /** @var CustomerMapperInterface */
    private $customerMapper;

    /** @var SellerMapperInterface */
    private $sellerMapper;

    /** @var TaxMapperInterface */
    private $taxMapper;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param MapperUtilities|null $utilities
     * @param CustomerMapperInterface|null $customerMapper
     * @param SellerMapperInterface|null $sellerMapper
     * @param TaxMapperInterface|null $taxMapper
     */
    public function __construct(
        MapperUtilities $utilities = null,
        CustomerMapperInterface $customerMapper = null,
        SellerMapperInterface $sellerMapper = null,
        TaxMapperInterface $taxMapper = null
    ) {
        $this->utilities = $utilities ?: new MapperUtilities();
        $this->customerMapper = $customerMapper ?: new CustomerMapper();
        $this->sellerMapper = $sellerMapper ?: new SellerMapper();
        $this->taxMapper = $taxMapper ?: new TaxMapper();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function build(\stdClass $map)
    {
        $object = new LineItem();
        if (isset($map->Customer)) {
            $object->setCustomer($this->customerMapper->build($map->Customer));
        }
        if (isset($map->Seller)) {
            $object->setSeller($this->sellerMapper->build($map->Seller));
        }
        if (isset($map->deliveryTerm)) {
            $object->setDeliveryTerm($map->deliveryTerm);
        }
        if (isset($map->lineItemId)) {
            $object->setLineItemId($map->lineItemId);
        }
        if (isset($map->locationCode)) {
            $object->setLocationCode($map->locationCode);
        }
        if (isset($map->Product)) {
            if ($map->Product instanceof \stdClass) {
                $object->setProductCode($map->Product->_);
                if (isset($map->Product->productClass)) {
                    $object->setProductClass($map->Product->productClass);
                }
            } else {
                $object->setProductCode($map->Product);
            }
        }
        if (isset($map->ExtendedPrice)) {
            $object->setExtendedPrice(
                $map->ExtendedPrice instanceof \stdClass ? $map->ExtendedPrice->_ : $map->ExtendedPrice
            );
        }
        if (isset($map->Quantity)) {
            $object->setQuantity(
                $map->Quantity instanceof \stdClass ? $map->Quantity->_ : $map->Quantity
            );
        }
        if (isset($map->TotalTax)) {
            $object->setTotalTax(
                $map->TotalTax instanceof \stdClass ? $map->TotalTax->_ : $map->TotalTax
            );
        }
        if (isset($map->UnitPrice)) {
            $object->setUnitPrice(
                $map->UnitPrice instanceof \stdClass ? $map->UnitPrice->_ : $map->UnitPrice
            );
        }

        if (isset($map->Taxes)) {
            $rawTaxes = $map->Taxes instanceof \stdClass ? [$map->Taxes] : $map->Taxes;
        } else {
            $rawTaxes = [];
        }

        $taxes = [];
        foreach ($rawTaxes as $rawTax) {
            $taxes[] = $this->taxMapper->build($rawTax);
        }
        $object->setTaxes($taxes);

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function map(LineItemInterface $object)
    {
        $map = new \stdClass();

        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getLocationCode(),
            'locationCode',
            0,
            20,
            true,
            'Location Code'
        );

        $map = $this->addDeliveryTermToMap($object, $map);

        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getLineItemId(),
            'lineItemId',
            1,
            40,
            true,
            'Line Item ID'
        );

        $this->addSellerToMap($map, $object);

        $this->addCustomerToMap($object, $map);

        $this->addProductToMap($object, $map);

        $map = $this->utilities->addToMapWithDecimalValidation(
            $map,
            $object->getQuantity(),
            'Quantity'
        );

        $map = $this->utilities->addToMapWithDecimalValidation(
            $map,
            $object->getUnitPrice(),
            'UnitPrice',
            PHP_INT_MIN,
            PHP_INT_MAX,
            true,
            'Unit Price'
        );

        $map = $this->utilities->addToMapWithDecimalValidation(
            $map,
            $object->getExtendedPrice(),
            'ExtendedPrice',
            PHP_INT_MIN,
            PHP_INT_MAX,
            true,
            'Extended Price'
        );

        $taxes = $object->getTaxes();
        $mapTaxes = [];
        foreach ($taxes as $tax) {
            $mapTaxes[] = $this->taxMapper->map($tax);
        }

        if (!empty($mapTaxes)) {
            $map->Taxes = $mapTaxes;
        }

        $map = $this->utilities->addToMapWithDecimalValidation(
            $map,
            $object->getTotalTax(),
            'TotalTax',
            PHP_INT_MIN,
            PHP_INT_MAX,
            true,
            'Total Tax'
        );
        return $map;
    }

    /**
     * Add Customer to SOAP map object
     *
     * @param LineItemInterface $object
     * @param \stdClass $map
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addCustomerToMap(LineItemInterface $object, \stdClass $map)
    {
        if ($object->getCustomer() !== null) {
            $map->Customer = $this->customerMapper->map($object->getCustomer());
        }
        return $map;
    }

    /**
     * Add the Delivery Term to the map
     *
     * @param LineItemInterface $object
     * @param \stdClass $map
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addDeliveryTermToMap(LineItemInterface $object, \stdClass $map)
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
     * Add Product to SOAP map object
     *
     * @param LineItemInterface $object
     * @param \stdClass $map
     * @return \stdClass
     */
    private function addProductToMap(LineItemInterface $object, \stdClass $map)
    {
        if ($object->getProductCode() !== null) {
            $map->Product = new \stdClass();
            $map->Product->_ = $object->getProductCode();
            if ($object->getProductClass() !== null) {
                $map->Product->productClass = $object->getProductClass();
            }
        }
        return $map;
    }

    /**
     * Add Seller to SOAP map object
     *
     * @param \stdClass $map
     * @param LineItemInterface $object
     * @return \stdClass
     * @throws \Vertex\Exception\ValidationException
     */
    private function addSellerToMap(\stdClass $map, LineItemInterface $object)
    {
        if ($object->getSeller() !== null) {
            $map->Seller = $this->sellerMapper->map($object->getSeller());
        }
        return $map;
    }
}
