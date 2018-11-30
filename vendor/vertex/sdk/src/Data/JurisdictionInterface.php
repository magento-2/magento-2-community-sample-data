<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Represents a Jurisdiction for which a tax applies
 *
 * @api
 */
interface JurisdictionInterface
{
    /** @var string Army Post Office */
    const JURISDICTION_LEVEL_APO = 'APO';

    /** @var string Borough */
    const JURISDICTION_LEVEL_BOROUGH = 'BOROUGH';

    /** @var string City */
    const JURISDICTION_LEVEL_CITY = 'CITY';

    /** @var string Country */
    const JURISDICTION_LEVEL_COUNTRY = 'COUNTRY';

    /** @var string County */
    const JURISDICTION_LEVEL_COUNTY = 'COUNTY';

    /** @var string District */
    const JURISDICTION_LEVEL_DISTRICT = 'DISTRICT';

    /** @var string Fleet Post Office */
    const JURISDICTION_LEVEL_FPO = 'FPO';

    /** @var string Local Improvement District */
    const JURISDICTION_LEVEL_LOCAL_IMPROVEMENT_DISTRICT = 'LOCAL_IMPROVEMENT_DISTRICT';

    /** @var string Parish */
    const JURISDICTION_LEVEL_PARISH = 'PARISH';

    /** @var string Province */
    const JURISDICTION_LEVEL_PROVINCE = 'PROVINCE';

    /** @var string Special Purporse District */
    const JURISDICTION_LEVEL_SPECIAL_PURPOSE_DISTRICT = 'SPECIAL_PURPOSE_DISTRICT';

    /** @var string State */
    const JURISDICTION_LEVEL_STATE = 'STATE';

    /** @var string Territory */
    const JURISDICTION_LEVEL_TERRITORY = 'TERRITORY';

    /** @var string Township */
    const JURISDICTION_LEVEL_TOWNSHIP = 'TOWNSHIP';

    /** @var string Trade Block */
    const JURISDICTION_LEVEL_TRADE_BLOCK = 'TRADE_BLOCK';

    /** @var string Transit District */
    const JURISDICTION_LEVEL_TRANSIT_DISTRICT = 'TRANSIT_DISTRICT';

    /**
     * Get the date when the tax for the jurisdiction became effective
     *
     * @return \DateTimeInterface
     */
    public function getEffectiveDate();

    /**
     * Get the date after which the tax for the jurisdiction is no longer effective
     *
     * @return \DateTimeInterface
     */
    public function getExpirationDate();

    /**
     * Get the jurisdiction code assigned by the relevant governmental authority
     *
     * @return string
     */
    public function getExternalJurisdictionCode();

    /**
     * Get the Vertex-specific number that identifies a jurisdiction
     *
     * @return int
     */
    public function getId();

    /**
     * Get the level of the jurisdiction for which the tax on the line item is applied
     *
     * Identifies the jurisdiction's common classification based on its geopolitical and/or taxing context.  They are
     * state, province, county, city, parish, districts.
     *
     * @return string
     */
    public function getLevel();

    /**
     * Get the name that this jurisdiction is commonly known.
     *
     * For example, Pennsylvania.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the date when the tax for the jurisdiction became effective
     *
     * @param \DateTimeInterface $effectiveDate
     * @return JurisdictionInterface
     */
    public function setEffectiveDate($effectiveDate);

    /**
     * Set the date after which the tax for the jurisdiction is no longer effective
     *
     * @param \DateTimeInterface $expirationDate
     * @return JurisdictionInterface
     */
    public function setExpirationDate($expirationDate);

    /**
     * Set the jurisdiction code assigned by the relevant governmental authority
     *
     * @param string $externalCode
     * @return JurisdictionInterface
     */
    public function setExternalJurisdictionCode($externalCode);

    /**
     * Set the Vertex-specific number that identifies a jurisdiction
     *
     * @param int $jurisdictionId
     * @return JurisdictionInterface
     */
    public function setId($jurisdictionId);

    /**
     * Set the level of the jurisdiction for which the tax on the line item is applied
     *
     * Identifies the jurisdiction's common classification based on its geopolitical and/or taxing context.  They are
     * state, province, county, city, parish, districts.
     *
     * @param string $level One of the JURISDICTION_LEVEL constants
     * @return JurisdictionInterface
     */
    public function setLevel($level);

    /**
     * Set the name that this jurisdiction is commonly known.
     *
     * For example, Pennsylvania.
     *
     * @param string $name
     * @return JurisdictionInterface
     */
    public function setName($name);
}
