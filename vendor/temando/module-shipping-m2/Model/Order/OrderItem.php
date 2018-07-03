<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Framework\DataObject;

/**
 * Temando Order Item
 *
 * An order item as associated with an order entity at the Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderItem extends DataObject implements OrderItemInterface
{
    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getData(OrderItemInterface::PRODUCT_ID);
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->getData(OrderItemInterface::QTY);
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->getData(OrderItemInterface::SKU);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(OrderItemInterface::NAME);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(OrderItemInterface::DESCRIPTION);
    }

    /**
     * @return string[]
     */
    public function getCategories()
    {
        return $this->getData(OrderItemInterface::CATEGORIES);
    }

    /**
     * @return string
     */
    public function getDimensionsUom()
    {
        return $this->getData(OrderItemInterface::DIMENSIONS_UOM);
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->getData(OrderItemInterface::LENGTH);
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->getData(OrderItemInterface::WIDTH);
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->getData(OrderItemInterface::HEIGHT);
    }

    /**
     * @return string
     */
    public function getWeightUom()
    {
        return $this->getData(OrderItemInterface::WEIGHT_UOM);
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->getData(OrderItemInterface::WEIGHT);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(OrderItemInterface::CURRENCY);
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->getData(OrderItemInterface::AMOUNT);
    }

    /**
     * @return bool
     */
    public function isFragile()
    {
        return $this->getData(OrderItemInterface::IS_FRAGILE);
    }

    /**
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getData(OrderItemInterface::IS_VIRTUAL);
    }

    /**
     * @return bool
     */
    public function isPrePackaged()
    {
        return $this->getData(OrderItemInterface::IS_PREPACKAGED);
    }

    /**
     * @return bool
     */
    public function canRotateVertically()
    {
        return $this->getData(OrderItemInterface::CAN_ROTATE_VERTICAL);
    }

    /**
     * @return string
     */
    public function getCountryOfOrigin()
    {
        return $this->getData(OrderItemInterface::COUNTRY_OF_ORIGIN);
    }

    /**
     * @return string
     */
    public function getCountryOfManufacture()
    {
        return $this->getData(OrderItemInterface::COUNTRY_OF_MANUFACTURE);
    }

    /**
     * @return string
     */
    public function getEccn()
    {
        return $this->getData(OrderItemInterface::ECCN);
    }

    /**
     * @return string
     */
    public function getScheduleBinfo()
    {
        return $this->getData(OrderItemInterface::SCHEDULE_B_INFO);
    }

    /**
     * @return string
     */
    public function getHsCode()
    {
        return $this->getData(OrderItemInterface::HS_CODE);
    }
}
