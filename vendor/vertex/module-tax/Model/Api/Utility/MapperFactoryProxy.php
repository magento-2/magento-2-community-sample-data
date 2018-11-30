<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Utility;

use Magento\Store\Model\ScopeInterface;
use Vertex\Mapper\MapperFactory;
use Vertex\Tax\Model\Api\ConfigBuilder;
use Vertex\Utility\VersionDeterminer;

/**
 * Retrieve a mapper based on Magento configuration
 */
class MapperFactoryProxy
{
    /** @var ConfigBuilder */
    private $configBuilder;

    /** @var MapperFactory */
    private $factory;

    /** @var VersionDeterminer */
    private $versionDeterminer;

    /**
     * @param MapperFactory $factory
     * @param VersionDeterminer $versionDeterminer
     * @param ConfigBuilder $configBuilder
     */
    public function __construct(
        MapperFactory $factory,
        VersionDeterminer $versionDeterminer,
        ConfigBuilder $configBuilder
    ) {
        $this->factory = $factory;
        $this->versionDeterminer = $versionDeterminer;
        $this->configBuilder = $configBuilder;
    }

    /**
     * Retrieve a mapper instance given a MapperInterface
     *
     * @param string $mapperInterface Mapper Interface to create
     * @param string|null $scopeCode Scope ID to use for configuration
     * @param string $scopeType Scope Type
     * @return mixed
     * @throws \Vertex\Exception\ConfigurationException
     */
    public function getForClass($mapperInterface, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        $config = $this->configBuilder
            ->setScopeCode($scopeCode)
            ->setScopeType($scopeType)
            ->build();

        $version = $this->versionDeterminer->execute($config->getTaxAreaLookupWsdl());

        return $this->factory->getForClass($mapperInterface, $version);
    }
}
