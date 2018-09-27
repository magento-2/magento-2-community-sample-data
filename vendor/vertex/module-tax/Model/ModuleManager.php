<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @copyright  Magento.  All rights reserved.
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * Determine if a Module is Enabled or not
 *
 * Fill in for {@see \Magento\Framework\Module\Manager} since it's not part of the public API.
 * See github PR #12677
 */
class ModuleManager
{
    /** @var DeploymentConfig */
    private $config;

    /** @var array */
    private $configData;

    /**
     * @param DeploymentConfig $config
     */
    public function __construct(DeploymentConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Determine if a module is enabled or not
     *
     * @param string $moduleName
     * @return bool
     */
    public function isEnabled($moduleName)
    {
        $this->loadModuleConfiguration();
        if (!$this->configData) {
            return false;
        }
        return !empty($this->configData[$moduleName]);
    }

    /**
     * Loads module configuration data
     */
    private function loadModuleConfiguration()
    {
        $this->config->resetData();
        if ($this->configData === null) {
            $this->configData = $this->config->get(ConfigOptionsListConstants::KEY_MODULES);
        }
    }
}
