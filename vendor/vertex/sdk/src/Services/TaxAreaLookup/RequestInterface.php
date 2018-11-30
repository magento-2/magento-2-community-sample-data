<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Services\TaxAreaLookup;

use Vertex\Data\AddressInterface;

/**
 * Contains a request to lookup a Tax Area
 *
 * @api
 */
interface RequestInterface
{
    /**
     * Get Postal Address
     *
     * @return AddressInterface|null
     */
    public function getPostalAddress();

    /**
     * Get Tax Area ID
     *
     * @return string|null
     */
    public function getTaxAreaId();

    /**
     * Set Postal Address
     *
     * @param AddressInterface $postalAddress
     * @return RequestInterface
     */
    public function setPostalAddress(AddressInterface $postalAddress);

    /**
     * Set Tax Area ID
     *
     * @param string $taxAreaId
     * @return RequestInterface
     */
    public function setTaxAreaId($taxAreaId);
}
