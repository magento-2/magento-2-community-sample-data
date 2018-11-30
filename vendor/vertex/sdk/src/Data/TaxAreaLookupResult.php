<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Default implementation of {@see TaxAreaLookupResultInterface}
 */
class TaxAreaLookupResult implements TaxAreaLookupResultInterface
{
    /** @var AddressInterface[] */
    private $addresses = [];

    /** @var \DateTimeInterface */
    private $asOfDate;

    /** @var int */
    private $confidenceIndicator;

    /** @var JurisdictionInterface[] */
    private $jurisdictions = [];

    /** @var string[] Array of STATUS constants */
    private $statuses = [];

    /** @var string */
    private $taxAreaId;

    /**
     * @inheritdoc
     */
    public function getAsOfDate()
    {
        return $this->asOfDate;
    }

    /**
     * @inheritdoc
     */
    public function getConfidenceIndicator()
    {
        return $this->confidenceIndicator;
    }

    /**
     * @inheritdoc
     */
    public function getJurisdictions()
    {
        return $this->jurisdictions;
    }

    /**
     * @inheritdoc
     */
    public function getPostalAddresses()
    {
        return $this->addresses;
    }

    /**
     * @inheritdoc
     */
    public function getStatuses()
    {
        return $this->statuses;
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
    public function setAsOfDate($asOfDate)
    {
        $this->asOfDate = $asOfDate;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setConfidenceIndicator($confidenceIndicator)
    {
        $this->confidenceIndicator = $confidenceIndicator;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setJurisdictions(array $jurisdictions)
    {
        array_walk(
            $jurisdictions,
            function ($item) {
                if (!($item instanceof JurisdictionInterface)) {
                    throw new \InvalidArgumentException(
                        'Lookup results must be instances of JurisdictionInterface'
                    );
                }
            }
        );
        $this->jurisdictions = $jurisdictions;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPostalAddresses(array $addresses)
    {
        array_walk(
            $addresses,
            function ($item) {
                if (!($item instanceof AddressInterface)) {
                    throw new \InvalidArgumentException(
                        'Lookup results must be instances of AddressInterface'
                    );
                }
            }
        );
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setStatuses(array $statuses)
    {
        $this->statuses = $statuses;
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
