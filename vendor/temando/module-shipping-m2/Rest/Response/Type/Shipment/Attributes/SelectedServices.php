<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment\Attributes;

/**
 * Temando API Shipment Selected ServicesResponse Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SelectedServices
{
    /**
     * @var string
     */
    private $configurationId;

    /**
     * @var string
     */
    private $integrationServiceId;

    /**
     * @return string
     */
    public function getConfigurationId()
    {
        return $this->configurationId;
    }

    /**
     * @param string $configurationId
     */
    public function setConfigurationId($configurationId)
    {
        $this->configurationId = $configurationId;
    }

    /**
     * @return string
     */
    public function getIntegrationServiceId()
    {
        return $this->integrationServiceId;
    }

    /**
     * @param string $integrationServiceId
     */
    public function setIntegrationServiceId($integrationServiceId)
    {
        $this->integrationServiceId = $integrationServiceId;
    }
}
