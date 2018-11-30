<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Default implementation of {@see JurisdictionInterface}
 */
class Jurisdiction implements JurisdictionInterface
{
    /** @var \DateTimeInterface */
    private $effectiveDate;

    /** @var \DateTimeInterface */
    private $expirationDate;

    /** @var string */
    private $externalJurisdictionCode;

    /** @var int */
    private $jurisdictionId;

    /** @var string */
    private $level;

    /** @var string */
    private $name;

    /**
     * @inheritdoc
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * @inheritdoc
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @inheritdoc
     */
    public function getExternalJurisdictionCode()
    {
        return $this->externalJurisdictionCode;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->jurisdictionId;
    }

    /**
     * @inheritdoc
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setExternalJurisdictionCode($jurisdictionCode)
    {
        $this->externalJurisdictionCode = $jurisdictionCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setId($jurisdictionId)
    {
        $this->jurisdictionId = $jurisdictionId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
