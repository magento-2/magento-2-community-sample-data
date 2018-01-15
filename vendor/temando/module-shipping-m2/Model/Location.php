<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;

/**
 * Temando Location Entity
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
class Location extends DataObject implements LocationInterface
{
    /**
     * @return string
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
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
    public function getUniqueIdentifier()
    {
        return $this->getData(self::UNIQUE_IDENTIFIER);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @return string[]
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->getData(self::POSTAL_CODE);
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }
}
