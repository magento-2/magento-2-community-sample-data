<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxQuote;

use Psr\Log\LoggerInterface;
use Vertex\Exception\ApiException;
use Vertex\Exception\ConfigurationException;
use Vertex\Exception\ValidationException;
use Vertex\Mapper\QuoteResponseMapperInterface;
use Vertex\Services\Quote\RequestInterface;
use Vertex\Services\Quote\ResponseInterface;
use Vertex\Tax\Api\QuoteInterface;
use Vertex\Tax\Model\Api\Utility\MapperFactoryProxy;
use Vertex\Tax\Model\TaxRegistry;

/**
 * Tax Quotation Request Service
 */
class TaxQuoteRequest
{
    /** @var CacheKeyGenerator */
    private $cacheKeyGenerator;

    /** @var LoggerInterface */
    private $logger;

    /** @var MapperFactoryProxy */
    private $mapperFactory;

    /** @var QuoteInterface */
    private $quote;

    /** @var TaxRegistry */
    private $taxRegistry;

    /**
     * @param QuoteInterface $quote
     * @param CacheKeyGenerator $cacheKeyGenerator
     * @param TaxRegistry $taxRegistry
     * @param LoggerInterface $logger
     * @param MapperFactoryProxy $mapperFactory
     */
    public function __construct(
        QuoteInterface $quote,
        CacheKeyGenerator $cacheKeyGenerator,
        TaxRegistry $taxRegistry,
        LoggerInterface $logger,
        MapperFactoryProxy $mapperFactory
    ) {
        $this->quote = $quote;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
        $this->taxRegistry = $taxRegistry;
        $this->logger = $logger;
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * Perform a Quotation Request
     *
     * @param RequestInterface $request
     * @param string|null $scopeCode
     * @return ResponseInterface|bool
     * @throws ApiException
     * @throws ValidationException
     * @throws ConfigurationException
     */
    public function taxQuote(RequestInterface $request, $scopeCode = null)
    {
        $cacheKey = false;
        $response = false;

        try {
            $cacheKey = $this->cacheKeyGenerator->generateCacheKey($request);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }

        if ($cacheKey !== false) {
            try {
                $response = $this->getCachedResponse($cacheKey, $scopeCode);
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            }
        }

        if (!$response) {
            try {
                $response = $this->quote->request($request, $scopeCode);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                throw $e;
            }

            $this->registerResponseInCache($cacheKey, $response, $scopeCode);
        }

        return $response;
    }

    /**
     * Retrieve the Response from the Cache
     *
     * @param string $cacheKey
     * @param string|null $scopeCode Store ID
     * @return ResponseInterface|bool
     */
    private function getCachedResponse($cacheKey, $scopeCode = null)
    {
        try {
            /** @var QuoteResponseMapperInterface $mapper */
            $mapper = $this->mapperFactory->getForClass(ResponseInterface::class, $scopeCode);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
        }

        $mappedResponse = $this->taxRegistry->lookup($cacheKey);

        return $mappedResponse !== null ? $mapper->build($mappedResponse) : false;
    }

    /**
     * Register the Response in the Cache
     *
     * @param string $cacheKey
     * @param ResponseInterface $response
     * @param string|null $scopeCode Store ID
     * @return void
     */
    private function registerResponseInCache($cacheKey, ResponseInterface $response, $scopeCode = null)
    {
        try {
            /** @var QuoteResponseMapperInterface $mapper */
            $mapper = $this->mapperFactory->getForClass(ResponseInterface::class, $scopeCode);
            $mappedResponse = $mapper->map($response);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return;
        }

        $this->taxRegistry->register($cacheKey, $mappedResponse);
    }
}
