<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Represents a Customer
 *
 * @api
 */
interface CustomerInterface
{
    /**
     * Retrieve the administrative destination
     *
     * The benefit received location for certain service transactions
     *
     * @return AddressInterface|null
     */
    public function getAdministrativeDestination();

    /**
     * Retrieve the code representing the customer
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Retrieve the destination
     *
     * Where the item is being shipped to, where the benefit is received, first used, where it is used, primary place of
     * use, principal use location, location of property, or place of use.
     *
     * @return AddressInterface|null
     */
    public function getDestination();

    /**
     * Retrieve the Tax Class for the customer
     *
     * @return string|null
     */
    public function getTaxClass();

    /**
     * Retrieve whether or not the customer is a business
     *
     * @return bool|null
     */
    public function isBusiness();

    /**
     * Set the administrative destination
     *
     * The benefit received location for certain service transactions
     *
     * @param AddressInterface $destination
     * @return CustomerInterface
     */
    public function setAdministrativeDestination(AddressInterface $destination);

    /**
     * Set the code representing the customer
     *
     * @param string $customerCode
     * @return CustomerInterface
     */
    public function setCode($customerCode);

    /**
     * Set the destination
     *
     * Where the item is being shipped to, where the benefit is received, first used, where it is used, primary place of
     * use, principal use location, location of property, or place of use.
     *
     * @param AddressInterface $destination
     * @return CustomerInterface
     */
    public function setDestination(AddressInterface $destination);

    /**
     * Set whether or not the customer is a business
     *
     * @param bool $isBusiness
     * @return CustomerInterface
     */
    public function setIsBusiness($isBusiness);

    /**
     * Set the Tax Class for the customer
     *
     * @param string $taxClass
     * @return CustomerInterface
     */
    public function setTaxClass($taxClass);
}
