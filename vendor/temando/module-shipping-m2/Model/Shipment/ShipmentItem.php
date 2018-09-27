<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Shipment Item Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package Temando\Shipping\Model
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com
 */
class ShipmentItem extends DataObject implements ShipmentItemInterface
{
    /**
     * @return int
     */
    public function getQty()
    {
        return $this->getData(PackageItemInterface::QTY);
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->getData(PackageItemInterface::SKU);
    }
}
