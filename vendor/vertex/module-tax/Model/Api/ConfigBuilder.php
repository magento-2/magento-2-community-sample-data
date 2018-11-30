<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api;

use Magento\Store\Model\ScopeInterface;
use Vertex\Data\ConfigurationInterface;
use Vertex\Data\ConfigurationInterfaceFactory;
use Vertex\Data\LoginInterface;
use Vertex\Data\LoginInterfaceFactory;
use Vertex\Tax\Model\Config as ModuleConfig;

/**
 * Creates a {@see ConfigurationInterface} for use with the Vertex API library
 */
class ConfigBuilder
{
    /** @var ConfigurationInterfaceFactory */
    private $configFactory;

    /** @var LoginInterfaceFactory */
    private $loginFactory;

    /** @var ModuleConfig */
    private $moduleConfig;

    /** @var string|null */
    private $scopeCode;

    /** @var string */
    private $scopeType = ScopeInterface::SCOPE_STORE;

    public function __construct(
        ModuleConfig $moduleConfig,
        ConfigurationInterfaceFactory $configFactory,
        LoginInterfaceFactory $loginFactory
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->configFactory = $configFactory;
        $this->loginFactory = $loginFactory;
    }

    /**
     * Create a {@see ConfigurationInterface} object for use with the Vertex API
     *
     * @return ConfigurationInterface
     */
    public function build()
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->configFactory->create();

        /** @var LoginInterface $login */
        $login = $this->loginFactory->create();

        $login->setTrustedId($this->moduleConfig->getTrustedId($this->scopeCode, $this->scopeType));

        $configuration->setLogin($login);
        $configuration->setTaxAreaLookupWsdl($this->getTaxAreaLookupWsdl());
        $configuration->setTaxCalculationWsdl($this->getTaxCalculationWsdl());

        return $configuration;
    }

    /**
     * Set the Scope Code
     *
     * @param string|null $scopeCode
     * @return ConfigBuilder
     */
    public function setScopeCode($scopeCode)
    {
        $this->scopeCode = $scopeCode;
        return $this;
    }

    /**
     * Set the Scope Type
     *
     * @param string|null $scopeType
     * @return ConfigBuilder
     */
    public function setScopeType($scopeType)
    {
        $this->scopeType = $scopeType;
        return $this;
    }

    /**
     * Assemble a URL
     *
     * @param string[] $urlParts indexed as parse_url would index them
     * @return string
     */
    private function assembleUrl($urlParts)
    {
        $url = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];
        if (isset($urlParts['query'])) {
            $url .= '?' . $urlParts['query'];
        }
        if (isset($urlParts['fragment'])) {
            $url .= '#' . $urlParts['fragment'];
        }
        return $url;
    }

    /**
     * Add a WSDL query parameter if one does not exist on the URL
     *
     * @param string $url
     * @return string
     */
    private function ensureWsdlQuery($url)
    {
        $urlParts = parse_url($url);
        $query = isset($urlParts['query']) ? $urlParts['query'] : null;
        $wsdlFound = false;

        if ($query !== null) {
            $queryParts = explode('&', $query);
            foreach ($queryParts as $parameter) {
                $parameterParts = explode('=', $parameter);
                $name = $parameterParts[0];
                if (strtolower($name) === 'wsdl') {
                    $wsdlFound = true;
                    break;
                }
            }
        }

        if (!$wsdlFound) {
            $urlParts['query'] = $query . (empty($query) ? 'wsdl' : '&wsdl');
        }

        return $this->assembleUrl($urlParts);
    }

    /**
     * Retrieve the Tax Area Lookup WSDL URL
     *
     * @return string
     */
    private function getTaxAreaLookupWsdl()
    {
        return $this->ensureWsdlQuery($this->moduleConfig->getVertexAddressHost($this->scopeCode, $this->scopeType));
    }

    /**
     * Retrieve the Tax Calculation WSDL URL
     *
     * @return string
     */
    private function getTaxCalculationWsdl()
    {
        return $this->ensureWsdlQuery($this->moduleConfig->getVertexHost($this->scopeCode, $this->scopeType));
    }
}
