<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;

/**
 * Temando Carrier Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Carrier extends DataObject implements CarrierInterface
{
    /**
     * @return string
     */
    public function getConfigurationId()
    {
        return $this->getData(self::CONFIGURATION_ID);
    }

    /**
     * @return string
     */
    public function getIntegrationId()
    {
        return $this->getData(self::INTEGRATION_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @return string
     */
    public function getConnectionName()
    {
        return $this->getData(self::CONNECTION_NAME);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string[]
     */
    public function getActiveServices()
    {
        return $this->getData(self::ACTIVE_SERVICES);
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->getData(self::LOGO);
    }
}
