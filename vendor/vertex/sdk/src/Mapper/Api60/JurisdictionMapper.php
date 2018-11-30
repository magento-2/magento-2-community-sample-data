<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\Jurisdiction;
use Vertex\Data\JurisdictionInterface;
use Vertex\Mapper\JurisdictionMapperInterface;
use Vertex\Mapper\MapperUtilities;

/**
 * API Level 60 implementation of {@see JurisdictionMapperInterface}
 */
class JurisdictionMapper implements JurisdictionMapperInterface
{
    /**
     * Maximum length of an external jurisdiction code
     */
    const EXTERNAL_JURISDICTION_CODE_MAX = 20;

    /**
     * Minimum length of an external jurisdiction code
     */
    const EXTERNAL_JURISDICTION_CODE_MIN = 1;

    /**
     * Maximim ID a Jurisdiction can have
     */
    const JURISDICTION_ID_MAX = 999999999;

    /**
     * Minimum ID a Jurisdiction can have
     */
    const JURISDICTION_ID_MIN = 0;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param MapperUtilities|null $utilities
     */
    public function __construct(MapperUtilities $utilities = null)
    {
        $this->utilities = $utilities ?: new MapperUtilities();
    }

    /**
     * @inheritdoc
     */
    public function build(\stdClass $map)
    {
        $jurisdiction = new Jurisdiction();
        if (isset($map->jurisdictionLevel)) {
            $jurisdiction->setLevel($map->jurisdictionLevel);
        }
        if (isset($map->jurisdictionId)) {
            $jurisdiction->setId($map->jurisdictionId);
        }
        if (isset($map->effectiveDate)) {
            $jurisdiction->setEffectiveDate(new \DateTime($map->effectiveDate));
        }
        if (isset($map->expirationDate)) {
            $jurisdiction->setExpirationDate(new \DateTime($map->expirationDate));
        }
        if (isset($map->externalJurisdictionCode)) {
            $jurisdiction->setExternalJurisdictionCode($map->externalJurisdictionCode);
        }
        if (isset($map->_)) {
            $jurisdiction->setName($map->_);
        }

        return $jurisdiction;
    }

    /**
     * @inheritdoc
     */
    public function map(JurisdictionInterface $object)
    {
        $map = new \stdClass();
        $map = $this->utilities->addToMapWithEnumerationValidation(
            $map,
            $object->getLevel(),
            'jurisdictionLevel',
            [
                JurisdictionInterface::JURISDICTION_LEVEL_APO,
                JurisdictionInterface::JURISDICTION_LEVEL_BOROUGH,
                JurisdictionInterface::JURISDICTION_LEVEL_CITY,
                JurisdictionInterface::JURISDICTION_LEVEL_COUNTRY,
                JurisdictionInterface::JURISDICTION_LEVEL_COUNTY,
                JurisdictionInterface::JURISDICTION_LEVEL_DISTRICT,
                JurisdictionInterface::JURISDICTION_LEVEL_FPO,
                JurisdictionInterface::JURISDICTION_LEVEL_LOCAL_IMPROVEMENT_DISTRICT,
                JurisdictionInterface::JURISDICTION_LEVEL_PARISH,
                JurisdictionInterface::JURISDICTION_LEVEL_PROVINCE,
                JurisdictionInterface::JURISDICTION_LEVEL_SPECIAL_PURPOSE_DISTRICT,
                JurisdictionInterface::JURISDICTION_LEVEL_STATE,
                JurisdictionInterface::JURISDICTION_LEVEL_TERRITORY,
                JurisdictionInterface::JURISDICTION_LEVEL_TOWNSHIP,
                JurisdictionInterface::JURISDICTION_LEVEL_TRADE_BLOCK,
                JurisdictionInterface::JURISDICTION_LEVEL_TRANSIT_DISTRICT
            ],
            false,
            'Jurisdiction level'
        );
        $map = $this->utilities->addToMapWithIntegerValidation(
            $map,
            $object->getId(),
            'jurisdictionId',
            static::JURISDICTION_ID_MIN,
            static::JURISDICTION_ID_MAX,
            false,
            'Jurisdiction ID'
        );
        $map = $this->utilities->addToMapWithDateValidation(
            $map,
            $object->getEffectiveDate(),
            'effectiveDate',
            true,
            'Effective Date'
        );
        $map = $this->utilities->addToMapWithDateValidation(
            $map,
            $object->getExpirationDate(),
            'expirationDate',
            true,
            'Expiration Date'
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getExternalJurisdictionCode(),
            'externalJurisdictionCode',
            static::EXTERNAL_JURISDICTION_CODE_MIN,
            static::EXTERNAL_JURISDICTION_CODE_MAX,
            true,
            'External Jurisdiction Code'
        );
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getName(),
            '_',
            MapperUtilities::DEFAULT_MIN,
            MapperUtilities::DEFAULT_MAX,
            true,
            'Name'
        );

        return $map;
    }
}
