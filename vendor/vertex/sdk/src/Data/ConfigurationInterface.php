<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Contains the configuration for interacting with Vertex
 *
 * @api
 */
interface ConfigurationInterface
{
    /**
     * Retrieve the configured Login object
     *
     * @return LoginInterface|null
     */
    public function getLogin();

    /**
     * Retrieve the URL configured for performing Tax Area Lookup requests
     *
     * @return string|null
     */
    public function getTaxAreaLookupWsdl();

    /**
     * Retrieve the URL configured for performing Tax Calculation requests
     *
     * @return string|null
     */
    public function getTaxCalculationWsdl();

    /**
     * Set a Login object to use when authenticating against Vertex APIs
     *
     * @param LoginInterface $login
     * @return ConfigurationInterface
     */
    public function setLogin(LoginInterface $login);

    /**
     * Set the URL to use when performing Tax Area Lookup requests
     *
     * @param string $url
     * @return ConfigurationInterface
     */
    public function setTaxAreaLookupWsdl($url);

    /**
     * Set the URL to use when performing Tax Calculation requests
     *
     * @param string $url
     * @return ConfigurationInterface
     */
    public function setTaxCalculationWsdl($url);
}
