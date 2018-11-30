<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Default implementation of {@see LineItemInterface}
 */
class LineItem implements LineItemInterface
{
    /** @var CustomerInterface */
    private $customer;

    /** @var string */
    private $deliveryTerm;

    /** @var float */
    private $extendedPrice;

    /** @var FlexibleFieldInterface[] */
    private $flexibleFields = [];

    /** @var int */
    private $lineItemId;

    /** @var string */
    private $locationCode;

    /** @var string */
    private $productClass;

    /** @var string */
    private $productCode;

    /** @var float */
    private $quantity;

    /** @var SellerInterface */
    private $seller;

    /** @var TaxInterface[] */
    private $taxes = [];

    /** @var float */
    private $totalTax;

    /** @var float */
    private $unitPrice;

    /**
     * @inheritdoc
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryTerm()
    {
        return $this->deliveryTerm;
    }

    /**
     * @inheritdoc
     */
    public function getExtendedPrice()
    {
        return $this->extendedPrice;
    }

    /**
     * @inheritdoc
     */
    public function getFlexibleFields()
    {
        return $this->flexibleFields;
    }

    /**
     * @inheritdoc
     */
    public function getLineItemId()
    {
        return $this->lineItemId;
    }

    /**
     * @inheritdoc
     */
    public function getLocationCode()
    {
        return $this->locationCode;
    }

    /**
     * @inheritdoc
     */
    public function getProductClass()
    {
        return $this->productClass;
    }

    /**
     * @inheritdoc
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritdoc
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @inheritdoc
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @inheritdoc
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * @inheritdoc
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @inheritdoc
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryTerm($deliveryTerm)
    {
        $this->deliveryTerm = $deliveryTerm;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setExtendedPrice($extendedPrice)
    {
        $this->extendedPrice = $extendedPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFlexibleFields(array $fields)
    {
        foreach ($fields as $field) {
            if (!($field instanceof FlexibleFieldInterface)) {
                throw new \InvalidArgumentException('Must be an array of FlexibleFieldInterface');
            }
        }
        $this->flexibleFields = $fields;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setLineItemId($lineItemId)
    {
        $this->lineItemId = $lineItemId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setProductClass($productClass)
    {
        $this->productClass = $productClass;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSeller(SellerInterface $seller)
    {
        $this->seller = $seller;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTaxes(array $taxes)
    {
        foreach ($taxes as $tax) {
            if (!($tax instanceof TaxInterface)) {
                throw new \InvalidArgumentException('Must be an array of TaxInterface');
            }
        }
        $this->taxes = $taxes;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTotalTax($totalTax)
    {
        $this->totalTax = $totalTax;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }
}
