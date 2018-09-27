<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Shipment Allocation Error
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @deprecated
 * @see ShipmentError
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AllocationError extends DataObject implements AllocationErrorInterface
{
    /**
     * Get attribute status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(AllocationErrorInterface::STATUS);
    }

    /**
     * Get attribute title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(AllocationErrorInterface::TITLE);
    }

    /**
     * Get attribute Code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData(AllocationErrorInterface::CODE);
    }

    /**
     * Get error detail (optional)
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->getData(AllocationErrorInterface::DETAIL);
    }
}
