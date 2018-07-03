<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\QuoteCollectionPoint as CollectionPointResource;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;

/**
 * Temando Quote Collection Point Entity
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class QuoteCollectionPoint extends AbstractModel implements QuoteCollectionPointInterface
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
