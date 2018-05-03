<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxQuote;

use Vertex\Tax\Api\ClientInterface;

/**
 * Tax Quotation Request Service
 */
class TaxQuoteRequest
{
    const REQUEST_TYPE = 'quote';

    /** @var ClientInterface */
    private $vertex;

    /** @var CacheKeyGenerator */
    private $cacheKeyGenerator;

    /**
     * Cache to hold the rates
     *
     * @var array
     */
    private $requestCache = [];

    /**
     * @param ClientInterface $vertex
     * @param CacheKeyGenerator $cacheKeyGenerator
     */
    public function __construct(ClientInterface $vertex, CacheKeyGenerator $cacheKeyGenerator)
    {
        $this->vertex = $vertex;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
    }

    /**
     * Perform a Quotation Request
     *
     * @param array $request
     * @return array|bool
     */
    public function taxQuote($request)
    {
        $cacheKey = $this->cacheKeyGenerator->generateCacheKey($request);

        if (!isset($this->requestCache[$cacheKey])) {
            $response = $this->vertex->sendApiRequest($request, static::REQUEST_TYPE);
            $this->requestCache[$cacheKey] = $response;
        }

        return $this->requestCache[$cacheKey];
    }
}
