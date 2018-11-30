<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Service;

use Vertex\Services\Quote;
use Vertex\Services\QuoteFactory as SdkQuoteFactory;
use Vertex\Tax\Model\Api\ConfigBuilder;

/**
 * Create a {@see Quote} service class
 */
class QuoteBuilder
{
    /** @var ConfigBuilder */
    private $configBuilder;

    /** @var string */
    private $scopeType;

    /** @var SdkQuoteFactory */
    private $sdkFactory;

    /** @var string */
    private $storeCode;

    /**
     * @param ConfigBuilder $configBuilder
     * @param SdkQuoteFactory $sdkFactory
     */
    public function __construct(
        ConfigBuilder $configBuilder,
        SdkQuoteFactory $sdkFactory
    ) {
        $this->configBuilder = $configBuilder;
        $this->sdkFactory = $sdkFactory;
    }

    /**
     * Create a Quote Service
     *
     * @return Quote
     */
    public function build()
    {
        $config = $this->configBuilder
            ->setScopeCode($this->storeCode)
            ->setScopeType($this->scopeType)
            ->build();

        return $this->sdkFactory->create(
            [
                'configuration' => $config,
            ]
        );
    }

    /**
     * Set the Scope Code
     *
     * @param string|null $scopeCode
     * @return QuoteBuilder
     */
    public function setScopeCode($scopeCode)
    {
        $this->storeCode = $scopeCode;
        return $this;
    }

    /**
     * Set the Scope Type
     *
     * @param string|null $scopeType
     * @return QuoteBuilder
     */
    public function setScopeType($scopeType)
    {
        $this->scopeType = $scopeType;
        return $this;
    }
}
