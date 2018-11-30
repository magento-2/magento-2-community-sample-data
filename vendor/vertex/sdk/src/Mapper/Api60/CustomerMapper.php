<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\AddressInterface;
use Vertex\Data\Customer;
use Vertex\Data\CustomerInterface;
use Vertex\Exception\ValidationException;
use Vertex\Mapper\AddressMapperInterface;
use Vertex\Mapper\CustomerMapperInterface;
use Vertex\Mapper\MapperUtilities;

/**
 * API Level 60 implementation of {@see CustomerMapperInterface}
 */
class CustomerMapper implements CustomerMapperInterface
{
    /**
     * Maximum length of Customer Tax Class
     */
    const CUSTOMER_CLASS_MAX = 40;

    /**
     * Minimum length of Customer Tax Class
     */
    const CUSTOMER_CLASS_MIN = 1;

    /**
     * Maximum length of Customer Code
     */
    const CUSTOMER_CODE_MAX = 40;

    /**
     * Minimum length of Customer Code
     */
    const CUSTOMER_CODE_MIN = 1;

    /** @var AddressMapper */
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
        $object = new Customer();

        if (isset($map->CustomerCode)) {
            if ($map->CustomerCode instanceof \stdClass) {
                $object->setCode($map->CustomerCode->_);

                if (isset($map->CustomerCode->isBusinessIndicator)) {
                    $object->setIsBusiness($map->CustomerCode->isBusinessIndicator);
                }

                if (isset($map->CustomerCode->classCode)) {
                    $object->setTaxClass($map->CustomerCode->classCode);
                }
            } else {
                $object->setCode($map->CustomerCode);
            }
        }

        if (isset($map->Destination)) {
            $object->setDestination($this->addressMapper->build($map->Destination));
        }

        if (isset($map->AdministrativeDestination)) {
            $object->setAdministrativeDestination($this->addressMapper->build($map->AdministrativeDestination));
        }

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function map(CustomerInterface $object)
    {
        $map = new \stdClass();

        $hasCode = $object->getCode() !== null;
        $hasBusiness = $object->isBusiness() !== null;
        $hasClass = $object->getTaxClass() !== null;

        if ($hasCode || $hasBusiness || $hasClass) {
            $map->CustomerCode = $this->utilities->addToMapWithLengthValidation(
                new \stdClass(),
                $object->getCode() ?: '',
                '_',
                self::CUSTOMER_CODE_MIN,
                self::CUSTOMER_CODE_MAX,
                false,
                'Customer Code'
            );

            if ($hasBusiness) {
                $map->CustomerCode->isBusinessIndicator = $object->isBusiness();
            }

            $map->CustomerCode = $this->utilities->addToMapWithLengthValidation(
                $map->CustomerCode,
                $object->getTaxClass(),
                'classCode',
                self::CUSTOMER_CLASS_MIN,
                self::CUSTOMER_CLASS_MAX,
                true,
                'Customer Tax Class'
            );
        }

        $map = $this->addAddressToMap(
            $map,
            $object->getDestination(),
            'Destination'
        );

        $map = $this->addAddressToMap(
            $map,
            $object->getAdministrativeDestination(),
            'AdministrativeDestination',
            true,
            'Administrative Destination'
        );

        return $map;
    }

    /**
     * Add Address to Mapping
     *
     * @param \stdClass $mapping
     * @param AddressInterface|null $value
     * @param string $key
     * @param bool $optional
     * @param string $name
     * @return \stdClass
     * @throws ValidationException
     */
    private function addAddressToMap(
        \stdClass $mapping,
        AddressInterface $value = null,
        $key,
        $optional = true,
        $name = null
    ) {
        $name = $name ?: $key;

        if ($value === null && !$optional) {
            throw new ValidationException("{$name} must not be null");
        }

        if ($value !== null) {
            $mapping->{$key} = $this->addressMapper->map($value);
        }

        return $mapping;
    }
}
