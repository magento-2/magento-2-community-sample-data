<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Default implementation of {@see ConfigurationInterface}
 */
class Configuration implements ConfigurationInterface
{
    /** @var LoginInterface|null */
    private $login;

    /** @var string|null */
    private $taxAreaLookupEndpoint;

    /** @var string|null */
    private $taxCalculationEndpoint;

    /**
     * @inheritdoc
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @inheritdoc
     */
    public function getTaxAreaLookupWsdl()
    {
        return $this->taxAreaLookupEndpoint;
    }

    /**
     * @inheritdoc
     */
    public function getTaxCalculationWsdl()
    {
        return $this->taxCalculationEndpoint;
    }

    /**
     * @inheritdoc
     */
    public function setLogin(LoginInterface $login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTaxAreaLookupWsdl($url)
    {
        $this->taxAreaLookupEndpoint = $url;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTaxCalculationWsdl($url)
    {
        $this->taxCalculationEndpoint = $url;
        return $this;
    }
}
