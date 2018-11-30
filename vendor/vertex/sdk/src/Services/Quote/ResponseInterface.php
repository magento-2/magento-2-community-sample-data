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
 * A response to a Quotation Request
 *
 * @api
 */
interface ResponseInterface
{
    /**
     * A transaction that results in the transfer of possession, but not title,
     * of tangible personal property (for consideration) to a customer for the
     * customer use for a specified time period.
     */
    const TRANSACTION_TYPE_LEASE = 'LEASE';

    /**
     * A transaction that results in the transfer of possession, not title, of
     * tangible personal property (for consideration) to a customer for use.
     * Rental time periods are typically shorter in duration as compared to
     * leases. For some jurisdictions, rental and lease are synonymous.
     */
    const TRANSACTION_TYPE_RENTAL = 'RENTAL';

    /**
     * A transaction that results in the passage of title, possession, or
     * service benefit from a seller to a buyer in exchange for consideration.
     */
    const TRANSACTION_TYPE_SALE = 'SALE';

    /**
     * Retrieve the Customer
     *
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * Retrieve the Delivery Term
     *
     * An identifier that determines the vendor or customer jurisdiction in which the title transfer of a supply takes
     * place. This is also known as Shipping Terms. Delivery Terms information could be critical to determine the place
     * of supply, for example, in distance selling. To calculate tax when the Physical Origin is in the US and the
     * Destination is in Canada, the deliveryTerm must be "SUP".
     *
     * @return string|null
     */
    public function getDeliveryTerm();

    /**
     * Retrieve the Document Date
     *
     * The date of the requested action
     *
     * @return \DateTimeInterface
     */
    public function getDocumentDate();

    /**
     * Retrieve the Document Number
     *
     * The document number in the host application that references the event
     *
     * @return string|null
     */
    public function getDocumentNumber();

    /**
     * Retrieve the Line Items in the transaction
     *
     * @return LineItemInterface[]
     */
    public function getLineItems();

    /**
     * Retrieve the location code
     *
     * A value that can be used for tax return filing in jurisdictions that require taxes to be filed for individual
     * retail locations.
     *
     * @return string|null
     */
    public function getLocationCode();

    /**
     * Retrieve the Seller
     *
     * @return SellerInterface
     */
    public function getSeller();

    /**
     * Retrieve the Subtotal
     *
     * @return float
     */
    public function getSubtotal();

    /**
     * Retrieve the Total
     *
     * @return float
     */
    public function getTotal();

    /**
     * Retrieve the Total amount of Tax
     *
     * @return float
     */
    public function getTotalTax();

    /**
     * Retrieve the Transaction ID
     *
     * @return string
     */
    public function getTransactionId();

    /**
     * Retrieve the Transaction Type
     *
     * @see RequestInterface::TRANSACTION_TYPE_LEASE
     * @see RequestInterface::TRANSACTION_TYPE_RENTAL
     * @see RequestInterface::TRANSACTION_TYPE_SALE
     * @return string
     */
    public function getTransactionType();

    /**
     * Set the Customer
     *
     * @param CustomerInterface $customer
     * @return RequestInterface
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * Set the Delivery Term
     *
     * An identifier that determines the vendor or customer jurisdiction in which the title transfer of a supply takes
     * place. This is also known as Shipping Terms. Delivery Terms information could be critical to determine the place
     * of supply, for example, in distance selling. To calculate tax when the Physical Origin is in the US and the
     * Destination is in Canada, the deliveryTerm must be "SUP".
     *
     * @param string $deliveryTerm
     * @return RequestInterface
     */
    public function setDeliveryTerm($deliveryTerm);

    /**
     * Set the Document Date
     *
     * The date of the requested action
     *
     * @param \DateTimeInterface $documentDate
     * @return RequestInterface
     */
    public function setDocumentDate($documentDate);

    /**
     * Set the Document Number
     *
     * The document number in the host application that references the event
     *
     * @param string $documentNumber
     * @return RequestInterface
     */
    public function setDocumentNumber($documentNumber);

    /**
     * Set the Line Items in the transaction
     *
     * @param LineItemInterface[] $lineItems
     * @return RequestInterface
     */
    public function setLineItems(array $lineItems);

    /**
     * Set the location code
     *
     * A value that can be used for tax return filing in jurisdictions that require taxes to be filed for individual
     * retail locations.
     *
     * @param string $locationCode
     * @return RequestInterface
     */
    public function setLocationCode($locationCode);

    /**
     * Set the Seller
     *
     * @param SellerInterface $seller
     * @return RequestInterface
     */
    public function setSeller(SellerInterface $seller);

    /**
     * Set the Subtotal
     *
     * @param float $subtotal
     * @return RequestInterface
     */
    public function setSubtotal($subtotal);

    /**
     * Set the Total
     *
     * @param float $total
     * @return RequestInterface
     */
    public function setTotal($total);

    /**
     * Set the Total Tax
     *
     * @param float $totalTax
     * @return RequestInterface
     */
    public function setTotalTax($totalTax);

    /**
     * Set the Transaction ID
     *
     * @param string $transactionId
     * @return RequestInterface
     */
    public function setTransactionId($transactionId);

    /**
     * Set the Transaction Type
     *
     * @see RequestInterface::TRANSACTION_TYPE_LEASE
     * @see RequestInterface::TRANSACTION_TYPE_RENTAL
     * @see RequestInterface::TRANSACTION_TYPE_SALE
     * @param string $transactionType
     * @return RequestInterface
     */
    public function setTransactionType($transactionType);
}
