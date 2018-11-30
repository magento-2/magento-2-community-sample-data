<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Represents a Line Item for a Quote or Invoice
 *
 * @api
 */
interface LineItemInterface
{
    /**
     * Retrieve the Customer
     *
     * @return CustomerInterface|null
     */
    public function getCustomer();

    /**
     * Retrieve the Delivery Term
     *
     * An identifier that determines the vendor or customer jurisdiction in which the title transfer of a supply takes
     * place. This is also known as Shipping Terms. Delivery Terms information could be critical to determine the place
     * of supply, for example, in distance selling.
     *
     * @return string|null
     */
    public function getDeliveryTerm();

    /**
     * Retrieve the actual price of the line item
     *
     * This field serves as a combination of unit price and quantity
     *
     * @return float|null
     */
    public function getExtendedPrice();

    /**
     * Retrieve any flexible fields attached to the LineItem
     *
     * @return FlexibleFieldInterface[]
     */
    public function getFlexibleFields();

    /**
     * Retrieve the identifier for the line item
     *
     * Used for synchronization purposes with the host system
     *
     * @return int|null
     */
    public function getLineItemId();

    /**
     * Retrieve the location code
     *
     * A string used for tax return filing in jurisdictions that require taxes to be filed for individual retail
     * locations
     *
     * @return string|null
     */
    public function getLocationCode();

    /**
     * Retrieve the Tax Class the product is a part of
     *
     * @return string|null
     */
    public function getProductClass();

    /**
     * Retrieve the code representing the product
     *
     * Typically a SKU
     *
     * @return string|null
     */
    public function getProductCode();

    /**
     * Retrieve the quantity of the product for the line item
     *
     * @return float|null
     */
    public function getQuantity();

    /**
     * Retrieve the Seller
     *
     * @return SellerInterface|null
     */
    public function getSeller();

    /**
     * Retrieve all Taxes on the line item
     *
     * @return TaxInterface[]
     */
    public function getTaxes();

    /**
     * Retrieve the Total Tax charged for the line item
     *
     * @return float|null
     */
    public function getTotalTax();

    /**
     * Retrieve the price per quantity
     *
     * @return float|null
     */
    public function getUnitPrice();

    /**
     * Set the Customer
     *
     * @param CustomerInterface $customer
     * @return LineItemInterface
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * Set the Delivery Term
     *
     * An identifier that determines the vendor or customer jurisdiction in which the title transfer of a supply takes
     * place. This is also known as Shipping Terms. Delivery Terms information could be critical to determine the place
     * of supply, for example, in distance selling.
     *
     * @param string $deliveryTerm
     * @return LineItemInterface
     */
    public function setDeliveryTerm($deliveryTerm);

    /**
     * Set the actual price of the line item
     *
     * This field serves as a combination of unit price and quantity
     *
     * @param float $extendedPrice
     * @return LineItemInterface
     */
    public function setExtendedPrice($extendedPrice);

    /**
     * Attach flexible fields to the LineItem
     *
     * @param FlexibleFieldInterface[] $fields
     * @return LineItemInterface
     */
    public function setFlexibleFields(array $fields);

    /**
     * Set the identifier for the line item
     *
     * Used for synchronization purposes with the host system
     *
     * @param string $lineItemId
     * @return LineItemInterface
     */
    public function setLineItemId($lineItemId);

    /**
     * Set the location code
     *
     * A string used for tax return filing in jurisdictions that require taxes to be filed for individual retail
     * locations
     *
     * @param string $locationCode
     * @return LineItemInterface
     */
    public function setLocationCode($locationCode);

    /**
     * Set the Tax Class the product is a part of
     *
     * @param string $productClass
     * @return LineItemInterface
     */
    public function setProductClass($productClass);

    /**
     * Set the code representing the product
     *
     * Typically a SKU
     *
     * @param string $productCode
     * @return LineItemInterface
     */
    public function setProductCode($productCode);

    /**
     * Set the quantity of the product for the line item
     *
     * @param float $quantity
     * @return LineItemInterface
     */
    public function setQuantity($quantity);

    /**
     * Set the Seller
     *
     * @param SellerInterface $seller
     * @return LineItemInterface
     */
    public function setSeller(SellerInterface $seller);

    /**
     * Set the Taxes on the line item
     *
     * @param TaxInterface[] $taxes
     * @return LineItemInterface
     */
    public function setTaxes(array $taxes);

    /**
     * Set the total tax charged for the line item
     *
     * @param float $totalTax
     * @return LineItemInterface
     */
    public function setTotalTax($totalTax);

    /**
     * Set the price per quantity
     *
     * @param float $unitPrice
     * @return LineItemInterface
     */
    public function setUnitPrice($unitPrice);
}
