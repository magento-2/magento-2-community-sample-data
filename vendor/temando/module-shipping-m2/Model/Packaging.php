<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;

/**
 * Temando Packaging Entity
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
class Packaging extends DataObject implements PackagingInterface
{
    /**
     * @return string
     */
    public function getPackagingId()
    {
        return $this->getData(self::PACKAGING_ID);
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
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @return string[]
     */
    public function getWidth()
    {
        return $this->getData(self::WIDTH);
    }

    /**
     * @return string
     */
    public function getLength()
    {
        return $this->getData(self::LENGTH);
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->getData(self::HEIGHT);
    }

    /**
     * @return string
     */
    public function getTareWeight()
    {
        return $this->getData(self::TARE_WEIGHT);
    }

    /**
     * @return string
     */
    public function getMaxWeight()
    {
        return $this->getData(self::MAX_WEIGHT);
    }
}
