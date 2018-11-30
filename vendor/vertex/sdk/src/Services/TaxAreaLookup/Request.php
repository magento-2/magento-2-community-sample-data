<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Services\TaxAreaLookup;

use Vertex\Data\AddressInterface;

/**
 * Default implementation of RequestInterface
 */
class Request implements RequestInterface
{
    /** @var AddressInterface */
    private $postalAddress;

    /** @var string */
    private $taxAreaId;

    /**
     * @inheritdoc
     */
    public function getPostalAddress()
    {
        return $this->postalAddress;
    }

    /**
     * @inheritdoc
     */
    public function getTaxAreaId()
    {
        return $this->taxAreaId;
    }

    /**
     * @inheritdoc
     */
    public function setPostalAddress(AddressInterface $postalAddress)
    {
        $this->postalAddress = $postalAddress;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTaxAreaId($taxAreaId)
    {
        $this->taxAreaId = $taxAreaId;
        return $this;
    }
}
