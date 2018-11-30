<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Services\Quote;

use Vertex\Data\CustomerInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Data\SellerInterface;

/**
 * Default implementation of {@see ResponseInterface}
 */
class Response implements ResponseInterface
{
    /** @var CustomerInterface */
    private $customer;

    /** @var string */
    private $deliveryTerm;

    /** @var \DateTimeInterface */
    private $documentDate;

    /** @var string */
    private $documentNumber;

    /** @var LineItemInterface[] */
    private $lineItems = [];

    /** @var string */
    private $locationCode;

    /** @var SellerInterface */
    private $seller;

    /** @var float */
    private $subtotal;

    /** @var float */
    private $total;

    /** @var float */
    private $totalTax;

    /** @var string */
    private $transactionId;

    /** @var string */
    private $transactionType;

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
    public function getDocumentDate()
    {
        return $this->documentDate;
    }

    /**
     * @inheritdoc
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    /**
     * @inheritdoc
     */
    public function getLineItems()
    {
        return $this->lineItems;
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
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @inheritdoc
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @inheritdoc
     */
    public function getTotal()
    {
        return $this->total;
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
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @inheritdoc
     */
    public function getTransactionType()
    {
        return $this->transactionType;
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
    public function setDocumentDate($documentDate)
    {
        $this->documentDate = $documentDate;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setLineItems(array $lineItems)
    {
        foreach ($lineItems as $lineItem) {
            if (!($lineItem instanceof LineItemInterface)) {
                throw new \InvalidArgumentException('Must be an array of LineItemInterface');
            }
        }
        $this->lineItems = $lineItems;
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
    public function setSeller(SellerInterface $seller)
    {
        $this->seller = $seller;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTotal($total)
    {
        $this->total = $total;
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
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }
}
