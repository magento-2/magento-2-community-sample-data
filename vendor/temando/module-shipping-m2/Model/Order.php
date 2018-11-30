<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;

/**
 * Temando Order Entity
 *
 * This model contains a subset of data that is used in the shipping module.
 * It does not contain all data as available in its platform representation.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Order extends DataObject implements OrderInterface
{
    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(OrderInterface::ORDER_ID);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(OrderInterface::CREATED_AT);
    }

    /**
     * @return string
     */
    public function getLastModifiedAt()
    {
        return $this->getData(OrderInterface::LAST_MODIFIED_AT);
    }

    /**
     * @return string
     */
    public function getOrderedAt()
    {
        return $this->getData(OrderInterface::ORDERED_AT);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(OrderInterface::STATUS);
    }

    /**
     * @return \Temando\Shipping\Model\Order\OrderBillingInterface
     */
    public function getBilling()
    {
        return $this->getData(OrderInterface::BILLING);
    }

    /**
     * @return \Temando\Shipping\Model\Order\OrderRecipientInterface
     */
    public function getRecipient()
    {
        return $this->getData(OrderInterface::RECIPIENT);
    }

    /**
     * @return \Temando\Shipping\Model\Order\OrderItemInterface[]
     */
    public function getOrderItems()
    {
        return $this->getData(OrderInterface::ORDER_ITEMS);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(OrderInterface::CURRENCY);
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->getData(OrderInterface::AMOUNT);
    }

    /**
     * @return int
     */
    public function getSourceReference()
    {
        return $this->getData(OrderInterface::SOURCE_REFERENCE);
    }

    /**
     * @return string
     */
    public function getSourceId()
    {
        return $this->getData(OrderInterface::SOURCE_ID);
    }

    /**
     * @return string
     */
    public function getSourceIncrementId()
    {
        return $this->getData(OrderInterface::SOURCE_INCREMENT_ID);
    }

    /**
     * @return \Temando\Shipping\Model\Checkout\Attribute\CheckoutFieldInterface[]
     */
    public function getCheckoutFields()
    {
        return $this->getData(OrderInterface::CHECKOUT_FIELDS);
    }

    /**
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface
     */
    public function getCollectionPoint()
    {
        return $this->getData(OrderInterface::COLLECTION_POINT);
    }

    /**
     * @return \Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface
     */
    public function getCollectionPointSearchRequest()
    {
        return $this->getData(OrderInterface::COLLECTION_POINT_SEARCH_REQUEST);
    }

    /**
     * @return string
     */
    public function getExperienceCode()
    {
        return $this->getData(OrderInterface::SELECTED_EXPERIENCE_CODE);
    }

    /**
     * @return string
     */
    public function getExperienceCurrency()
    {
        return $this->getData(OrderInterface::SELECTED_EXPERIENCE_CURRENCY);
    }

    /**
     * @return float
     */
    public function getExperienceAmount()
    {
        return $this->getData(OrderInterface::SELECTED_EXPERIENCE_AMOUNT);
    }

    /**
     * @return string
     */
    public function getExperienceLanguage()
    {
        return $this->getData(OrderInterface::SELECTED_EXPERIENCE_LANGUAGE);
    }

    /**
     * @return string
     */
    public function getExperienceDescription()
    {
        return $this->getData(OrderInterface::SELECTED_EXPERIENCE_DESCRIPTION);
    }
}
