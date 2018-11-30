<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\AddressInterface;
use Vertex\Data\JurisdictionInterface;
use Vertex\Data\TaxAreaLookupResult;
use Vertex\Data\TaxAreaLookupResultInterface;
use Vertex\Mapper\AddressMapperInterface;
use Vertex\Mapper\JurisdictionMapperInterface;
use Vertex\Mapper\MapperUtilities;
use Vertex\Mapper\TaxAreaLookupResultMapperInterface;

/**
 * API Level 60 implementation of {@see TaxAreaLookupResultMapperInterface}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxAreaLookupResultMapper implements TaxAreaLookupResultMapperInterface
{
    /**
     * Maximum value of the Confidence Indicator
     */
    const CONFIDENCE_INDICATOR_MAX = 100;

    /**
     * Minimum value of the Confidence Indicator
     */
    const CONFIDENCE_INDICATOR_MIN = 0;

    /** @var AddressMapperInterface */
    private $addressMapper;

    /** @var JurisdictionMapperInterface */
    private $jurisdictionMapper;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param MapperUtilities|null $utilities
     * @param JurisdictionMapperInterface|null $jurisdictionMapper
     * @param AddressMapperInterface|null $addressMapper
     */
    public function __construct(
        MapperUtilities $utilities = null,
        JurisdictionMapperInterface $jurisdictionMapper = null,
        AddressMapperInterface $addressMapper = null
    ) {
        $this->utilities = $utilities ?: new MapperUtilities();
        $this->jurisdictionMapper = $jurisdictionMapper ?: new JurisdictionMapper();
        $this->addressMapper = $addressMapper ?: new AddressMapper();
    }

    /**
     * @inheritdoc
     */
    public function build(\stdClass $map)
    {
        $result = new TaxAreaLookupResult();

        $map = $this->buildJurisdictions($result, $map);

        $map = $this->buildAddresses($result, $map);

        $map = $this->buildStatuses($result, $map);

        if (isset($map->taxAreaId)) {
            $result->setTaxAreaId($map->taxAreaId);
        }
        if (isset($map->asOfDate)) {
            $result->setAsOfDate(new \DateTime($map->asOfDate));
        }
        if (isset($map->confidenceIndicator)) {
            $result->setConfidenceIndicator($map->confidenceIndicator);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function map(TaxAreaLookupResultInterface $object)
    {
        $map = new \stdClass();

        $jurisdictions = $this->mapJurisdictions($object->getJurisdictions());
        if (count($jurisdictions) > 0) {
            $map->Jurisdiction = count($jurisdictions) > 1 ? $jurisdictions : reset($jurisdictions);
        }

        $addresses = $this->mapAddresses($object->getPostalAddresses());
        if (count($addresses) > 0) {
            $map->PostalAddress = count($addresses) > 1 ? $addresses : reset($addresses);
        }

        $statuses = $object->getStatuses();
        foreach ($statuses as $status) {
            $mapStatus = new \stdClass();
            $mapStatus->lookupResult = (string)$status;
            $map->Status[] = $mapStatus;
        }
        if (count($statuses) === 1) {
            $map->Status = $map->Status[0];
        }

        $map = $this->utilities->addToMapWithIntegerValidation(
            $map,
            $object->getTaxAreaId(),
            'taxAreaId',
            TaxAreaLookupRequestMapper::TAX_AREA_ID_MIN,
            TaxAreaLookupRequestMapper::TAX_AREA_ID_MAX,
            true,
            'Tax Area ID'
        );
        $map = $this->utilities->addToMapWithDateValidation(
            $map,
            $object->getAsOfDate(),
            'asOfDate',
            true,
            'As-of Date'
        );
        $map = $this->utilities->addToMapWithIntegerValidation(
            $map,
            $object->getConfidenceIndicator(),
            'confidenceIndicator',
            static::CONFIDENCE_INDICATOR_MIN,
            static::CONFIDENCE_INDICATOR_MAX,
            true,
            'Confidence Indicator'
        );

        return $map;
    }

    /**
     * Add a SOAP compatible array of postal addresses to a {@see TaxAreaLookupResultInterface}
     *
     * @param TaxAreaLookupResultInterface $result
     * @param \stdClass $map
     * @return \stdClass
     */
    private function buildAddresses(TaxAreaLookupResultInterface $result, \stdClass $map)
    {
        $mapAddresses = isset($map->PostalAddress) ? $map->PostalAddress : [];
        if (!is_array($mapAddresses)) {
            $mapAddresses = [$mapAddresses];
        }
        $addresses = [];
        foreach ($mapAddresses as $mapAddress) {
            $addresses[] = $this->addressMapper->build($mapAddress);
        }
        $result->setPostalAddresses($addresses);
        return $map;
    }

    /**
     * Add a SOAP compatible array of jurisdictions to a {@see TaxAreaLookupResultInterface}
     *
     * @param TaxAreaLookupResultInterface $result
     * @param \stdClass $map
     * @return \stdClass
     */
    private function buildJurisdictions(TaxAreaLookupResultInterface $result, \stdClass $map)
    {
        $mapJurisdictions = isset($map->Jurisdiction) ? $map->Jurisdiction : [];
        if (!is_array($mapJurisdictions)) {
            $mapJurisdictions = [$mapJurisdictions];
        }
        $jurisdictions = [];
        foreach ($mapJurisdictions as $mapJurisdiction) {
            $jurisdictions[] = $this->jurisdictionMapper->build($mapJurisdiction);
        }
        $result->setJurisdictions($jurisdictions);
        return $map;
    }

    /**
     * Add a SOAP compatible array of statuses to a {@see TaxAreaLookupResultInterface}
     *
     * @param TaxAreaLookupResultInterface $result
     * @param \stdClass $map
     * @return \stdClass
     */
    private function buildStatuses(TaxAreaLookupResultInterface $result, \stdClass $map)
    {
        $mapStatuses = isset($map->Status) ? $map->Status : [];
        if (!is_array($mapStatuses)) {
            $mapStatuses = [$mapStatuses];
        }
        $statuses = [];
        foreach ($mapStatuses as $mapStatus) {
            $statuses[] = (string)$mapStatus->lookupResult;
        }
        $result->setStatuses($statuses);
        return $map;
    }

    /**
     * Build a SOAP compatible array of Address objects from a {@see TaxAreaLookupResultInterface}
     *
     * @param AddressInterface[] $addresses
     * @return \stdClass[]
     * @throws \Vertex\Exception\ValidationException
     */
    private function mapAddresses(array $addresses)
    {
        $mappedAddresses = [];
        foreach ($addresses as $address) {
            $mappedAddresses[] = $this->addressMapper->map($address);
        }
        return $mappedAddresses;
    }

    /**
     * Build a SOAP compatible array of Jurisdiction objects from a {@see TaxAreaLookupResultInterface}
     *
     * @param JurisdictionInterface[] $jurisdictions
     * @return \stdClass[]
     * @throws \Vertex\Exception\ValidationException
     */
    private function mapJurisdictions(array $jurisdictions)
    {
        $mappedJurisdictions = [];
        foreach ($jurisdictions as $jurisdiction) {
            $mappedJurisdictions[] = $this->jurisdictionMapper->map($jurisdiction);
        }
        return $mappedJurisdictions;
    }
}
