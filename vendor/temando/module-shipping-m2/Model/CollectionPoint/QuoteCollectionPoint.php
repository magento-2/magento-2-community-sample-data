<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\DataObject;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;

/**
 * Temando Quote Collection Point Entity
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @deprecated since 1.4.0
 * @see \Temando\Shipping\Model\Delivery\QuoteCollectionPoint
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class QuoteCollectionPoint extends DataObject implements QuoteCollectionPointInterface
{
    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(QuoteCollectionPointInterface::ENTITY_ID);
    }

    /**
     * @return string
     */
    public function getCollectionPointId()
    {
        return $this->getData(QuoteCollectionPointInterface::COLLECTION_POINT_ID);
    }

    /**
     * @return int
     */
    public function getRecipientAddressId()
    {
        return $this->getData(QuoteCollectionPointInterface::RECIPIENT_ADDRESS_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(QuoteCollectionPointInterface::NAME);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(QuoteCollectionPointInterface::COUNTRY);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(QuoteCollectionPointInterface::REGION);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(QuoteCollectionPointInterface::POSTCODE);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData(QuoteCollectionPointInterface::CITY);
    }

    /**
     * @return string[]
     */
    public function getStreet()
    {
        return $this->getData(QuoteCollectionPointInterface::STREET);
    }

    /**
     * @return string[][]
     */
    public function getOpeningHours()
    {
        return $this->getData(QuoteCollectionPointInterface::OPENING_HOURS);
    }

    /**
     * @return string[][]
     */
    public function getShippingExperiences()
    {
        return $this->getData(QuoteCollectionPointInterface::SHIPPING_EXPERIENCES);
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        return $this->getData(QuoteCollectionPointInterface::SELECTED);
    }
}
