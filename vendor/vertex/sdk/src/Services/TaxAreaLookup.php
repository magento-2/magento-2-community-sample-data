<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Services;

use Vertex\Data\ConfigurationInterface;
use Vertex\Exception\ApiException;
use Vertex\Exception\ConfigurationException;
use Vertex\Exception\ValidationException;
use Vertex\Mapper\AuthenticatorInterface;
use Vertex\Mapper\MapperFactory;
use Vertex\Mapper\TaxAreaLookupRequestMapperInterface;
use Vertex\Mapper\TaxAreaLookupResponseMapperInterface;
use Vertex\Services\TaxAreaLookup\RequestInterface;
use Vertex\Services\TaxAreaLookup\ResponseInterface;
use Vertex\Utility\ServiceActionPerformerFactory;
use Vertex\Utility\VersionDeterminer;

/**
 * Look up a Tax Area
 *
 * @api
 */
class TaxAreaLookup
{
    /** @var ServiceActionPerformerFactory */
    private $actionPerformerFactory;

    /** @var ConfigurationInterface */
    private $configuration;

    /** @var MapperFactory */
    private $mapperFactory;

    /** @var VersionDeterminer */
    private $versionDeterminer;

    /**
     * @param ConfigurationInterface $configuration
     * @param MapperFactory|null $mapperFactory
     * @param VersionDeterminer|null $versionDeterminer
     * @param ServiceActionPerformerFactory|null $actionPerformerFactory
     */
    public function __construct(
        ConfigurationInterface $configuration,
        MapperFactory $mapperFactory = null,
        VersionDeterminer $versionDeterminer = null,
        ServiceActionPerformerFactory $actionPerformerFactory = null
    ) {
        $this->configuration = $configuration;
        $this->mapperFactory = $mapperFactory ?: new MapperFactory();
        $this->versionDeterminer = $versionDeterminer ?: new VersionDeterminer();
        $this->actionPerformerFactory = $actionPerformerFactory ?: new ServiceActionPerformerFactory();
    }

    /**
     * Perform a Tax Area Lookup
     *
     * @param RequestInterface $request
     * @return ResponseInterface $response
     * @throws ApiException
     * @throws ValidationException
     * @throws ConfigurationException
     */
    public function lookup(RequestInterface $request)
    {
        $this->validateConfiguration();

        return $this->actionPerformerFactory->create(
            [
                'url' => $this->configuration->getTaxAreaLookupWsdl(),
                'method' => $this->getCall(),
                'requestMapper' => $this->getRequestMapper(),
                'responseMapper' => $this->getResponseMapper(),
                'authenticator' => $this->getAuthenticator(),
            ]
        )->performService($this->configuration->getLogin(), $request);
    }

    /**
     * Retrieve the API version string
     *
     * Used to determine methods and mappers used when performing the API calls
     *
     * @return string
     * @throws ConfigurationException
     */
    private function getApiVersion()
    {
        return $this->versionDeterminer->execute($this->configuration->getTaxAreaLookupWsdl());
    }

    /**
     * Retrieve the Authentication Mapper
     *
     * @return AuthenticatorInterface
     * @throws ConfigurationException
     */
    private function getAuthenticator()
    {
        return $this->mapperFactory->getForClass('Vertex\Mapper\AuthenticatorInterface', $this->getApiVersion());
    }

    /**
     * Retrieve the SOAP method to call
     *
     * @return string
     * @throws ConfigurationException
     */
    private function getCall()
    {
        return 'LookupTaxAreas' . $this->getApiVersion();
    }

    /**
     * Retrieve the Request Mapper
     *
     * @return TaxAreaLookupRequestMapperInterface
     * @throws ConfigurationException
     */
    private function getRequestMapper()
    {
        return $this->mapperFactory->getForClass(
            'Vertex\Services\TaxAreaLookup\RequestInterface',
            $this->getApiVersion()
        );
    }

    /**
     * Retrieve the Response Mapper
     *
     * @return TaxAreaLookupResponseMapperInterface
     * @throws ConfigurationException
     */
    private function getResponseMapper()
    {
        return $this->mapperFactory->getForClass(
            'Vertex\Services\TaxAreaLookup\ResponseInterface',
            $this->getApiVersion()
        );
    }

    /**
     * Validate that we have all configuration necessary for performing the call
     *
     * @return void
     * @throws ConfigurationException
     */
    private function validateConfiguration()
    {
        if ($this->configuration->getLogin() === null) {
            throw new ConfigurationException('Login required for TaxAreaLookup call');
        }
        if ($this->configuration->getTaxAreaLookupWsdl() === null) {
            throw new ConfigurationException('TaxAreaLookup WSDL required for Invoice call');
        }
    }
}
