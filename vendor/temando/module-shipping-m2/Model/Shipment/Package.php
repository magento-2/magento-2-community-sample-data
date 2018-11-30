<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Package Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package Temando\Shipping\Model
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com
 */
class Package extends DataObject implements PackageInterface
{
    /**
     * @return string
     */
    public function getPackageId()
    {
        return $this->getData(self::PACKAGE_ID);
    }

    /**
     * @return string
     */
    public function getTrackingReference()
    {
        return $this->getData(self::TRACKING_REFERENCE);
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->getData(self::WIDTH);
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->getData(self::LENGTH);
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->getData(self::HEIGHT);
    }

    /**
     * @return PackageItemInterface[]
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }
}
