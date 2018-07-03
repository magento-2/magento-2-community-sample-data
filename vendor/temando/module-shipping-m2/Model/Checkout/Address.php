<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Model\ResourceModel\Checkout\Address as AddressResource;

/**
 * Checkout shipping address extension
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Address extends AbstractModel implements AddressInterface
{
    /**
     * Init resource model.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(AddressResource::class);
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(AddressInterface::ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return void
     */
    public function setEntityId($entityId)
    {
        $this->setData(AddressInterface::ENTITY_ID, $entityId);
    }

    /**
     * @return int
     */
    public function getShippingAddressId()
    {
        return $this->getData(AddressInterface::SHIPPING_ADDRESS_ID);
    }

    /**
     * @param int $addressId
     * @return void
     */
    public function setShippingAddressId($addressId)
    {
        $this->setData(AddressInterface::SHIPPING_ADDRESS_ID, $addressId);
    }

    /**
     * @return \Magento\Framework\Api\AttributeInterface[]
     */
    public function getServiceSelection()
    {
        return $this->getData(AddressInterface::SERVICE_SELECTION);
    }

    /**
     * @param \Magento\Framework\Api\AttributeInterface[] $services
     * @return void
     */
    public function setServiceSelection(array $services)
    {
        $this->setData(AddressInterface::SERVICE_SELECTION, $services);
    }
}
