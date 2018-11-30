<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\Seller;
use Vertex\Data\SellerInterface;
use Vertex\Mapper\AddressMapperInterface;
use Vertex\Mapper\MapperUtilities;
use Vertex\Mapper\SellerMapperInterface;

/**
 * API Level 60 implementation of {@see SellerMapperInterface}
 */
class SellerMapper implements SellerMapperInterface
{
    /**
     * Maximum length for a Company Code
     */
    const COMPANY_CODE_MAX = 40;

    /**
     * Minimum length for a Company Code
     */
    const COMPANY_CODE_MIN = 1;

    /** @var AddressMapperInterface */
    private $addressMapper;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param MapperUtilities|null $utilities
     * @param AddressMapperInterface|null $addressMapper
     */
    public function __construct(MapperUtilities $utilities = null, AddressMapperInterface $addressMapper = null)
    {
        $this->utilities = $utilities ?: new MapperUtilities();
        $this->addressMapper = $addressMapper ?: new AddressMapper();
    }

    /**
     * @inheritdoc
     */
    public function build(\stdClass $map)
    {
        $object = new Seller();

        if (isset($map->Company)) {
            $object->setCompanyCode($map->Company instanceof \stdClass ? $map->Company->_ : $map->Company);
        }

        if (isset($map->PhysicalOrigin)) {
            $object->setPhysicalOrigin($this->addressMapper->build($map->PhysicalOrigin));
        }

        if (isset($map->AdministrativeOrigin)) {
            $object->setAdministrativeOrigin($this->addressMapper->build($map->AdministrativeOrigin));
        }

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function map(SellerInterface $object)
    {
        $map = new \stdClass();

        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getCompanyCode(),
            'Company',
            self::COMPANY_CODE_MIN,
            self::COMPANY_CODE_MAX,
            true
        );

        if ($object->getPhysicalOrigin() !== null) {
            $map->PhysicalOrigin = $this->addressMapper->map($object->getPhysicalOrigin());
        }

        if ($object->getAdministrativeOrigin() !== null) {
            $map->AdministrativeOrigin = $this->addressMapper->map($object->getAdministrativeOrigin());
        }

        return $map;
    }
}
