<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Utility;

use Vertex\Data\LoginInterface;
use Vertex\Exception\ApiException;
use Vertex\Mapper\AuthenticatorInterface;

/**
 * Contains the primary logic for performing a Vertex SOAP Request
 *
 * Contains the logic for creating the SOAP Client, performing the request, mapping the request and response,
 * authenticating the request and handling SoapFaults
 */
class ServiceActionPerformer
{
    /** @var AuthenticatorInterface */
    private $authenticator;

    /** @var SoapFaultConverterInterface */
    private $faultConverter;

    /** @var string */
    private $method;

    /** @var object */
    private $requestMapper;

    /** @var object */
    private $responseMapper;

    /** @var SoapClientFactory */
    private $soapClientFactory;

    /** @var string */
    private $url;

    /**
     * @param string $url
     * @param string $method
     * @param object $requestMapper A mapper for the request (Expects a method ->map)
     * @param object $responseMapper A mapper for the response (Expects a method ->build)
     * @param AuthenticatorInterface $authenticator
     * @param SoapClientFactory|null $soapClientFactory
     * @param SoapFaultConverterInterface|null $faultConverter
     */
    public function __construct(
        $url,
        $method,
        $requestMapper,
        $responseMapper,
        AuthenticatorInterface $authenticator,
        SoapClientFactory $soapClientFactory = null,
        SoapFaultConverterInterface $faultConverter = null
    ) {
        if (!is_object($requestMapper) || !method_exists($requestMapper, 'map')) {
            throw new \InvalidArgumentException('Invalid requestMapper provided.  Must have a "map" method');
        }

        if (!is_object($responseMapper) || !method_exists($responseMapper, 'build')) {
            throw new \InvalidArgumentException('Invalid responseMapper provided.  Must have a "build" method');
        }

        $this->url = $url;
        $this->method = $method;
        $this->requestMapper = $requestMapper;
        $this->responseMapper = $responseMapper;
        $this->authenticator = $authenticator;
        $this->soapClientFactory = $soapClientFactory ?: new SoapClientFactory();
        $this->faultConverter = $faultConverter ?: (new SoapFaultConverterBuilder())->build();
    }

    /**
     * Performs a Vertex SOAP request
     *
     * @param LoginInterface $login
     * @param object $request A request object compatible with requestMapper specified during construction
     * @return object A response object resulting from construct specified responseMapper's build method
     * @throws ApiException
     * @throws \Vertex\Exception\ValidationException
     */
    public function performService(LoginInterface $login, $request)
    {
        try {
            $client = $this->soapClientFactory->create($this->url);
        } catch (\SoapFault $e) {
            $convertedFault = $this->faultConverter->convert($e);
            throw $convertedFault ?: new ApiException($e->getMessage(), 0, $e);
        }
        $rawRequest = $this->requestMapper->map($request);
        $rawRequest = $this->authenticator->addLogin($rawRequest, $login);

        try {
            $rawResponse = $client->{$this->method}($rawRequest);
        } catch (\SoapFault $e) {
            $convertedFault = $this->faultConverter->convert($e);
            throw $convertedFault ?: new ApiException($e->getMessage(), 0, $e);
        }

        return $this->responseMapper->build($rawResponse);
    }
}
