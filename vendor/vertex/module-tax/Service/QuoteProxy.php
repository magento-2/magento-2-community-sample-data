<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Service;

use Magento\Store\Model\ScopeInterface;
use Vertex\Services\Quote\RequestInterface;
use Vertex\Tax\Api\QuoteInterface;
use Vertex\Tax\Model\Api\Logger;
use Vertex\Tax\Model\Api\Service\QuoteBuilder;

/**
 * Default implementation of {@see QuoteInterface}
 */
class QuoteProxy implements QuoteInterface
{
    /** @var Logger */
    private $logger;

    /** @var QuoteBuilder */
    private $quoteBuilder;

    /**
     * @param Logger $logger
     * @param QuoteBuilder $quoteBuilder
     */
    public function __construct(Logger $logger, QuoteBuilder $quoteBuilder)
    {
        $this->logger = $logger;
        $this->quoteBuilder = $quoteBuilder;
    }

    /**
     * @inheritdoc
     */
    public function request(RequestInterface $request, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        $quote = $this->quoteBuilder
            ->setScopeCode($scopeCode)
            ->setScopeType($scopeType)
            ->build();

        return $this->logger->wrapCall(
            function () use ($quote, $request) {
                return $quote->request($request);
            },
            'quote'
        );
    }
}
