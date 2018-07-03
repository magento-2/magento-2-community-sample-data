<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxQuote;

use Vertex\Tax\Api\ClientInterface;
use Vertex\Tax\Model\TaxRegistry;

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

    /** @var TaxRegistry */
    private $taxRegistry;

    /**
     * @param ClientInterface $vertex
     * @param CacheKeyGenerator $cacheKeyGenerator
     * @param TaxRegistry $taxRegistry
     */
    public function __construct(
        ClientInterface $vertex,
        CacheKeyGenerator $cacheKeyGenerator,
        TaxRegistry $taxRegistry
    ) {
        $this->vertex = $vertex;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
        $this->taxRegistry = $taxRegistry;
    }

    /**
     * Perform a Quotation Request
     *
     * @param array $request
     * @return array|bool
     */
    public function taxQuote(array $request)
    {
        $cacheKey = $this->cacheKeyGenerator->generateCacheKey($request);
        $response = $this->taxRegistry->lookup($cacheKey);

        if (!$response) {
            $response = $this->vertex->sendApiRequest($request, static::REQUEST_TYPE);

            $this->taxRegistry->unregister($cacheKey);
            $this->taxRegistry->register($cacheKey, $response);
        }

        return $response;
    }
}
