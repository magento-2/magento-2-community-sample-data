<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Service;

use Magento\Store\Model\ScopeInterface;
use Vertex\Services\TaxAreaLookup\RequestInterface;
use Vertex\Tax\Api\TaxAreaLookupInterface;
use Vertex\Tax\Model\Api\Logger;
use Vertex\Tax\Model\Api\Service\TaxAreaLookupBuilder;

/**
 * Default implementation of {@see TaxAreaLookupInterface}
 */
class TaxAreaLookupProxy implements TaxAreaLookupInterface
{
    /** @var Logger */
    private $logger;

    /** @var TaxAreaLookupBuilder */
    private $lookupBuilder;

    /**
     * @param Logger $logger
     * @param TaxAreaLookupBuilder $lookupBuilder
     */
    public function __construct(Logger $logger, TaxAreaLookupBuilder $lookupBuilder)
    {
        $this->logger = $logger;
        $this->lookupBuilder = $lookupBuilder;
    }

    /**
     * @inheritdoc
     */
    public function lookup(RequestInterface $request, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        $taxAreaLookup = $this->lookupBuilder
            ->setScopeCode($scopeCode)
            ->setScopeType($scopeType)
            ->build();

        return $this->logger->wrapCall(
            function () use ($taxAreaLookup, $request) {
                return $taxAreaLookup->lookup($request);
            },
            'tax_area_lookup'
        );
    }
}
