<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Default implementation of {@see SellerInterface}
 */
class Seller implements SellerInterface
{
    /** @var AddressInterface */
    private $administrativeOrigin;

    /** @var string */
    private $companyCode;

    /** @var AddressInterface */
    private $physicalOrigin;

    /**
     * @inheritdoc
     */
    public function getAdministrativeOrigin()
    {
        return $this->administrativeOrigin;
    }

    /**
     * @inheritdoc
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * @inheritdoc
     */
    public function getPhysicalOrigin()
    {
        return $this->physicalOrigin;
    }

    /**
     * @inheritdoc
     */
    public function setAdministrativeOrigin(AddressInterface $administrativeOrigin)
    {
        $this->administrativeOrigin = $administrativeOrigin;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPhysicalOrigin(AddressInterface $physicalOrigin)
    {
        $this->physicalOrigin = $physicalOrigin;
        return $this;
    }
}
