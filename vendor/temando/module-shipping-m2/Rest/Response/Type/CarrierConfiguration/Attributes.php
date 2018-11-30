<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CarrierConfiguration;

/**
 * Temando API Carrier Configuration Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Attributes
{
    /**
     * @var string
     */
    private $integrationId;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $connectionName;

    /**
     * @var string[]
     */
    private $integrationServiceIds = [];

    /**
     * @return string
     */
    public function getIntegrationId()
    {
        return $this->integrationId;
    }

    /**
     * @param string $integrationId
     * @return void
     */
    public function setIntegrationId($integrationId)
    {
        $this->integrationId = $integrationId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * @param string $connectionName
     * @return void
     */
    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;
    }

    /**
     * @return string[]
     */
    public function getIntegrationServiceIds()
    {
        return $this->integrationServiceIds;
    }

    /**
     * @param string[] $integrationServiceIds
     * @return void
     */
    public function setIntegrationServiceIds(array $integrationServiceIds)
    {
        $this->integrationServiceIds = $integrationServiceIds;
    }
}
