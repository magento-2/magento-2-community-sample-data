<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Package Item Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package Temando\Shipping\Model
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com
 */
class PackageItem extends DataObject implements PackageItemInterface
{
    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getData(PackageItemInterface::PRODUCT_ID);
    }

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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(PackageItemInterface::NAME);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(PackageItemInterface::DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getData(PackageItemInterface::CATEGORY_NAME);
    }

    /**
     * @return string
     */
    public function getDimensionsUom()
    {
        return $this->getData(PackageItemInterface::DIMENSIONS_UOM);
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->getData(PackageItemInterface::LENGTH);
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->getData(PackageItemInterface::WIDTH);
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->getData(PackageItemInterface::HEIGHT);
    }

    /**
     * @return string
     */
    public function getWeightUom()
    {
        return $this->getData(PackageItemInterface::WEIGHT_UOM);
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->getData(PackageItemInterface::WEIGHT);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(PackageItemInterface::CURRENCY);
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->getData(PackageItemInterface::AMOUNT);
    }

    /**
     * @return bool
     */
    public function isFragile()
    {
        return $this->getData(PackageItemInterface::IS_FRAGILE);
    }

    /**
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getData(PackageItemInterface::IS_VIRTUAL);
    }

    /**
     * @return bool
     */
    public function isPrePackaged()
    {
        return $this->getData(PackageItemInterface::IS_PREPACKAGED);
    }

    /**
     * @return bool
     */
    public function canRotateVertically()
    {
        return $this->getData(PackageItemInterface::CAN_ROTATE_VERTICAL);
    }

    /**
     * @return string
     */
    public function getCountryOfOrigin()
    {
        return $this->getData(PackageItemInterface::COUNTRY_OF_ORIGIN);
    }

    /**
     * @return string
     */
    public function getCountryOfManufacture()
    {
        return $this->getData(PackageItemInterface::COUNTRY_OF_MANUFACTURE);
    }

    /**
     * @return string
     */
    public function getEccn()
    {
        return $this->getData(PackageItemInterface::ECCN);
    }

    /**
     * @return string
     */
    public function getScheduleBinfo()
    {
        return $this->getData(PackageItemInterface::SCHEDULE_B_INFO);
    }

    /**
     * @return string
     */
    public function getHsCode()
    {
        return $this->getData(PackageItemInterface::HS_CODE);
    }

    /**
     * @return string
     */
    public function getMonetaryValue()
    {
        return $this->getData(PackageItemInterface::MONETARY_VALUE);
    }
}
