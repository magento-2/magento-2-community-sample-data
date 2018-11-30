<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Represents a result from a Tax Area Lookup
 *
 * @api
 */
interface TaxAreaLookupResultInterface
{
    /**
     * The location information used during the lookup was incomplete or inconsistent.
     */
    const STATUS_BAD_REGION_FIELDS = 'BAD_REGION_FIELDS';

    /**
     * The street address used during the lookup were incomplete or inconsistent.
     */
    const STATUS_BAD_STREET_INFORMATION = 'BAD_STREET_INFORMATION';

    /**
     * The confidence indicator values were suppressed,
     *
     * The feature is not supported for the input criteria provided.
     */
    const STATUS_CONFIDENCE_INDICATOR_SUPPRESSED = 'CONFIDENCE_INDICATOR_SUPPRESSED';

    /**
     * Some of the location information was missing and may have been ignored during the lookup.
     */
    const STATUS_IGNORED_REGION_FIELDS = 'IGNORED_REGION_FIELDS';

    /**
     * The maximum number of full addresses was exceeded.
     *
     * The maximum number of full addresses that can be returned during a lookup is specified in the
     * taxgis.jurisdictionfinder.MaximumFullAddresses parameter. The default value for this parameter is 100. For
     * details on the configuration parameters, refer to the Configuration File Parameters Reference Guide.
     */
    const STATUS_MAX_FULL_ADDRESSES_EXCEEDED = 'MAX_FULL_ADDRESSES_EXCEEDED';

    /**
     * The maximum number of tax areas was exceeded.
     *
     * The maximum number of tax areas that can be returned during a lookup is specified in the
     * taxgis.jurisdictionfinder.MaximumTaxAreas parameter. The default value for this parameter is 5. For details on
     * the configuration parameters, refer to the Configuration File Parameters Reference Guide.
     */
    const STATUS_MAX_TAXAREAS_EXCEEDED = 'MAX_TAXAREAS_EXCEEDED';

    /**
     * The minimum aggregate confidence has been exceeded.
     *
     * The minimum aggregate confidence indicator for returning tax areas is specified in the
     * taxgis.jurisdictionfinder.MinimumAggregateConfidence parameter. The default value for this parameter is 100. For
     * details on the configuration parameters, refer to the Configuration File Parameters Reference Guide.
     */
    const STATUS_MIN_AGGREGATE_CONFIDENCE_EXCEEDED = 'MIN_AGGREGATE_CONFIDENCE_EXCEEDED';

    /**
     * The lookup returned at least one tax area.
     */
    const STATUS_NORMAL = 'NORMAL';

    /**
     * The confidence indicator feature has been disabled.
     *
     * The transaction date precedes the feature implementation date.
     */
    const STATUS_PRECEDES_CONFIDENCE_INDICATOR_FUNCTIONALITY = 'PRECEDES_CONFIDENCE_INDICATOR_FUNCTIONALITY';

    /**
     * Get As Of Date
     *
     * @return \DateTimeInterface|null
     */
    public function getAsOfDate();

    /**
     * Get Confidence Indicator
     *
     * @return int|null
     */
    public function getConfidenceIndicator();

    /**
     * Get Jurisdictions
     *
     * @return JurisdictionInterface[]
     */
    public function getJurisdictions();

    /**
     * Get Postal Addresses
     *
     * @return AddressInterface[]
     */
    public function getPostalAddresses();

    /**
     * Get Statuses
     *
     * One or more of the STATUS constants
     *
     * @return string[]
     */
    public function getStatuses();

    /**
     * Get Tax Area ID
     *
     * @return string|null
     */
    public function getTaxAreaId();

    /**
     * Set As Of Date
     *
     * @param \DateTimeInterface
     * @return TaxAreaLookupResultInterface
     */
    public function setAsOfDate($asOfDate);

    /**
     * Set Confidence Indicator
     *
     * @param int $confidenceIndicator
     * @return TaxAreaLookupResultInterface
     */
    public function setConfidenceIndicator($confidenceIndicator);

    /**
     * Set Jurisdictions
     *
     * @param JurisdictionInterface[] $jurisdictions
     * @return TaxAreaLookupResultInterface
     */
    public function setJurisdictions(array $jurisdictions);

    /**
     * Set Postal Addresses
     *
     * @param AddressInterface[] $addresses
     * @return TaxAreaLookupResultInterface
     */
    public function setPostalAddresses(array $addresses);

    /**
     * Set Statuses
     *
     * One or more of the STATUS constants
     *
     * @param string[] $statuses
     * @return TaxAreaLookupResultInterface
     */
    public function setStatuses(array $statuses);

    /**
     * Set Tax Area ID
     *
     * @param string $taxAreaId
     * @return TaxAreaLookupResultInterface
     */
    public function setTaxAreaId($taxAreaId);
}
