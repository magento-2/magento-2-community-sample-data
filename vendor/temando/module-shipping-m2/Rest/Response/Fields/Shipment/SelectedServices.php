<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Shipment;

/**
 * Temando API Shipment Selected Services Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
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
     * @return void
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
     * @return void
     */
    public function setIntegrationServiceId($integrationServiceId)
    {
        $this->integrationServiceId = $integrationServiceId;
    }
}
