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
use Vertex\Mapper\QuoteRequestMapperInterface;
use Vertex\Mapper\QuoteResponseMapperInterface;
use Vertex\Services\Quote\RequestInterface;
use Vertex\Services\Quote\ResponseInterface;
use Vertex\Utility\ServiceActionPerformerFactory;
use Vertex\Utility\VersionDeterminer;

/**
 * Retrieve a Quote for Taxes
 *
 * @api
 */
class Quote
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
     * Request a tax Quote
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws ApiException
     * @throws ValidationException
     * @throws ConfigurationException
     */
    public function request(RequestInterface $request)
    {
        $this->validateConfiguration();

        return $this->actionPerformerFactory->create(
            [
                'url' => $this->configuration->getTaxCalculationWsdl(),
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
        return $this->versionDeterminer->execute($this->configuration->getTaxCalculationWsdl());
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
        return 'CalculateTax' . $this->getApiVersion();
    }

    /**
     * Retrieve the Request Mapper
     *
     * @return QuoteRequestMapperInterface
     * @throws ConfigurationException
     */
    private function getRequestMapper()
    {
        return $this->mapperFactory->getForClass(
            'Vertex\Services\Quote\RequestInterface',
            $this->getApiVersion()
        );
    }

    /**
     * Retrieve the Response Mapper
     *
     * @return QuoteResponseMapperInterface
     * @throws ConfigurationException
     */
    private function getResponseMapper()
    {
        return $this->mapperFactory->getForClass(
            'Vertex\Services\Quote\ResponseInterface',
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
            throw new ConfigurationException('Login required for Invoice call');
        }
        if ($this->configuration->getTaxCalculationWsdl() === null) {
            throw new ConfigurationException('Tax Calculation WSDL required for Invoice call');
        }
    }
}
