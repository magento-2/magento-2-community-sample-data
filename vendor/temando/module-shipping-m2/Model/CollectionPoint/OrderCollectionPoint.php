<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\DataObject;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterface;

/**
 * Temando Order Collection Point Entity
 *
 * @deprecated since 1.4.0
 * @see \Temando\Shipping\Model\Delivery\OrderCollectionPoint
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderCollectionPoint extends DataObject implements OrderCollectionPointInterface
{
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
