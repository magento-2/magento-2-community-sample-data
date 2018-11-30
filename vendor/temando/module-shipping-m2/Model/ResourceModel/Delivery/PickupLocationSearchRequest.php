<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchRequestInterface;
use Temando\Shipping\Model\ResourceModel\Db\NoSequenceDb;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Pickup Location Search Request Resource Model
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocationSearchRequest extends NoSequenceDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            SetupSchema::TABLE_PICKUP_LOCATION_SEARCH,
            PickupLocationSearchRequestInterface::SHIPPING_ADDRESS_ID
        );
    }
}
