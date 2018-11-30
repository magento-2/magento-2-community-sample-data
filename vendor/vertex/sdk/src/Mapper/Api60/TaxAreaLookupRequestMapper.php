<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Mapper\AddressMapperInterface;
use Vertex\Mapper\MapperUtilities;
use Vertex\Mapper\TaxAreaLookupRequestMapperInterface;
use Vertex\Services\TaxAreaLookup\Request;
use Vertex\Services\TaxAreaLookup\RequestInterface;

/**
 * API Level 60 implementation of {@see TaxAreaLookupRequestMapperInterface}
 */
class TaxAreaLookupRequestMapper implements TaxAreaLookupRequestMapperInterface
{
    /**
     * Maximum int possible for a Tax Area ID
     */
    const TAX_AREA_ID_MAX = 999999999;

    /**
     * Minimum int possible for a Tax Area ID
     */
    const TAX_AREA_ID_MIN = 0;

    /** @var AddressMapperInterface */
    private $addressMapper;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param AddressMapperInterface|null $addressMapper
     * @param MapperUtilities|null $utilities
     */
    public function __construct(AddressMapperInterface $addressMapper = null, MapperUtilities $utilities = null)
    {
        $this->addressMapper = $addressMapper ?: new AddressMapper();
        $this->utilities = $utilities ?: new MapperUtilities();
    }

    /**
     * @inheritdoc
     */
    public function build(\stdClass $map)
    {
        $map = $map->TaxAreaRequest->TaxAreaLookup;
        $request = new Request();
        if (isset($map->PostalAddress)) {
            $postalAddress = $this->addressMapper->build($map->PostalAddress);
            $request->setPostalAddress($postalAddress);
        }
        if (isset($map->TaxAreaId)) {
            $request->setTaxAreaId($map->TaxAreaId);
        }

        return $request;
    }

    /**
     * @inheritdoc
     */
    public function map(RequestInterface $object)
    {
        $lookup = new \stdClass();
        $addressInfo = $object->getPostalAddress();
        if ($addressInfo !== null) {
            $lookup->PostalAddress = $this->addressMapper->map($addressInfo);
        }

        $lookup = $this->utilities->addToMapWithIntegerValidation(
            $lookup,
            $object->getTaxAreaId(),
            'TaxAreaId',
            static::TAX_AREA_ID_MIN,
            static::TAX_AREA_ID_MAX,
            true,
            'Tax Area ID'
        );

        $map = new \stdClass();
        $map->TaxAreaRequest = new \stdClass();
        $map->TaxAreaRequest->TaxAreaLookup = $lookup;
        return $map;
    }
}
