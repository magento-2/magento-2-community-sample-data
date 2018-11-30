<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchRequestInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\PickupLocationSearchRequest as SearchRequestResource;

/**
 * Temando Pickup Location Search Request
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocationSearchRequest extends AbstractModel implements PickupLocationSearchRequestInterface
{
    /**
     * Init resource model.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(SearchRequestResource::class);
    }

    /**
     * @return int
     */
    public function getShippingAddressId()
    {
        return $this->getData(PickupLocationSearchRequestInterface::SHIPPING_ADDRESS_ID);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getData(PickupLocationSearchRequestInterface::ACTIVE);
    }
}
