<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxQuote;

use Magento\Store\Model\ScopeInterface;
use Vertex\Mapper\QuoteRequestMapperInterface;
use Vertex\Services\Quote\RequestInterface;
use Vertex\Tax\Model\Api\Utility\MapperFactoryProxy;

/**
 * Generates a cache storage key for a Quotation Request
 */
class CacheKeyGenerator
{
    /** @var MapperFactoryProxy */
    private $mapperFactory;

    /**
     * @param MapperFactoryProxy $mapperFactory
     */
    public function __construct(MapperFactoryProxy $mapperFactory)
    {
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * Convert a Tax Quote Request into a string for caching
     *
     * @param RequestInterface $request
     * @param string|null $scopeCode Store ID
     * @param string $scopeType Scope Type
     * @return string
     * @throws \Vertex\Exception\ValidationException
     * @throws \Vertex\Exception\ConfigurationException
     */
    public function generateCacheKey(
        RequestInterface $request,
        $scopeCode = null,
        $scopeType = ScopeInterface::SCOPE_STORE
    ) {
        /** @var QuoteRequestMapperInterface $mapper */
        $mapper = $this->mapperFactory->getForClass(RequestInterface::class, $scopeCode, $scopeType);
        return sha1(json_encode($mapper->map($request)));
    }
}
