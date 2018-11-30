<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Represents a Seller
 *
 * @api
 */
interface SellerInterface
{
    /**
     * Retrieve the Administrative Origin
     *
     * Where the order was taken/accepted, the place of principle negotiation or place of business
     *
     * @return AddressInterface|null
     */
    public function getAdministrativeOrigin();

    /**
     * Retrieve the Company Code
     *
     * @return string|null
     */
    public function getCompanyCode();

    /**
     * Retrieve the Physical Origin
     *
     * Where the order is shipped from or first-removed.
     *
     * @return AddressInterface|null
     */
    public function getPhysicalOrigin();

    /**
     * Set the Administrative Origin
     *
     * Where the order was taken/accepted, the place of principle negotiation or place of business
     *
     * @param AddressInterface $administrativeOrigin
     * @return SellerInterface
     */
    public function setAdministrativeOrigin(AddressInterface $administrativeOrigin);

    /**
     * Set the Company Code
     *
     * @param string $companyCode
     * @return SellerInterface
     */
    public function setCompanyCode($companyCode);

    /**
     * Set the Physical Origin
     *
     * Where the order is shipped from or first-removed
     *
     * @param AddressInterface $physicalOrigin
     * @return SellerInterface
     */
    public function setPhysicalOrigin(AddressInterface $physicalOrigin);
}
