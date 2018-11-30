<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\Delivery\OrderCollectionPointInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\OrderCollectionPoint as CollectionPointResource;

/**
 * Temando Order Collection Point Entity
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderCollectionPoint extends AbstractModel implements OrderCollectionPointInterface
{
    /**
     * Init resource model.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(CollectionPointResource::class);
    }

    /**
     * @return int
     */
    public function getRecipientAddressId()
    {
        return $this->getData(OrderCollectionPointInterface::RECIPIENT_ADDRESS_ID);
    }

    /**
     * @return string
     */
    public function getCollectionPointId()
    {
        return $this->getData(OrderCollectionPointInterface::COLLECTION_POINT_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(OrderCollectionPointInterface::NAME);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(OrderCollectionPointInterface::COUNTRY);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(OrderCollectionPointInterface::REGION);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(OrderCollectionPointInterface::POSTCODE);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData(OrderCollectionPointInterface::CITY);
    }

    /**
     * @return string[]
     */
    public function getStreet()
    {
        return $this->getData(OrderCollectionPointInterface::STREET);
    }
}
