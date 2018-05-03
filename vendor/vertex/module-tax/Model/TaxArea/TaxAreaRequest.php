<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxArea;

use Magento\Store\Model\StoreManagerInterface;
use Vertex\Tax\Api\ClientInterface;
use Vertex\Tax\Model\Config;

/**
 * Formatter for Tax Area Requests against the Vertex API
 */
class TaxAreaRequest
{
    const REQUEST_TYPE = 'tax_area_lookup';

    /** @var ClientInterface */
    private $vertex;

    /** @var TaxAreaResponseFactory */
    private $responseFactory;

    /** @var Config */
    private $config;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * Cache to hold the rates
     *
     * @var array
     */
    private $requestCache = [];

    /**
     * @param Config $config
     * @param ClientInterface $vertex
     * @param TaxAreaResponseFactory $responseFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        ClientInterface $vertex,
        TaxAreaResponseFactory $responseFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->vertex = $vertex;
        $this->responseFactory = $responseFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Create a properly formatted Tax Area Request
     *
     * @param array $address
     * @return array
     */
    private function getFormattedRequest(array $address)
    {
        $request = [
            'Login' => [
                'TrustedId' => $this->config->getTrustedId()
            ],
            'TaxAreaRequest' => [
                'TaxAreaLookup' => [
                    'PostalAddress' => $address
                ]
            ]
        ];

        return $request;
    }

    /**
     * Lookup the Tax Area for an Address
     *
     * @param array $address
     * @return bool|TaxAreaResponse
     */
    public function taxAreaLookup(array $address)
    {
        $cacheKey = $this->getRequestCacheKey($address);

        if (!isset($this->requestCache[$cacheKey])) {
            $requestData = $this->getFormattedRequest($address);
            $apiResponse = $this->vertex->sendApiRequest($requestData, static::REQUEST_TYPE);

            if (!$apiResponse) {
                return false;
            }

            /** @var TaxAreaResponse $response */
            $response = $this->responseFactory->create();
            $response->parseResponse($apiResponse, $requestData);

            $this->requestCache[$cacheKey] = $response;
        } else {
            $response = $this->requestCache[$cacheKey];
        }
        return $response;
    }

    /**
     * Get cache key value for specific address request
     *
     * @param array $address
     *
     * @return string
     */
    private function getRequestCacheKey(array $address)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $keys = [$storeId];
        if (isset($address['StreetAddress1'])) {
            $keys[] = $address['StreetAddress1'];
        }
        if (isset($address['StreetAddress2'])) {
            $keys[] = $address['StreetAddress2'];
        }
        if (isset($address['Country'])) {
            $keys[] = $address['Country'];
        }
        if (isset($address['City'])) {
            $keys[] = $address['City'];
        }
        if (isset($address['MainDivision'])) {
            $keys[] = $address['MainDivision'];
        }
        if (isset($address['PostalCode'])) {
            $keys[] = $address['PostalCode'];
        }

        return sha1(implode('|', $keys));
    }
}
