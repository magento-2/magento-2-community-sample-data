<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Package Item Interface.
 *
 * The package data object represents one part of the shipment packages list.
 *
 * @package Temando\Shipping\Model
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com
 */
interface PackageItemInterface
{
    const PRODUCT_ID = 'product_id';
    const QTY = 'qty';
    const SKU = 'sku';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const CATEGORY_NAME = 'category_name';

    const DIMENSIONS_UOM = 'dimensions_uom';
    const LENGTH = 'length';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const WEIGHT_UOM = 'weight_uom';
    const WEIGHT = 'weight';
    const CURRENCY = 'currency';
    const AMOUNT = 'amount';

    const IS_FRAGILE = 'is_fragile';
    const IS_VIRTUAL = 'is_virtual';
    const IS_PREPACKAGED = 'is_prepackaged';
    const CAN_ROTATE_VERTICAL = 'can_rotate_vertical';

    const COUNTRY_OF_ORIGIN = 'country_of_origin';
    const COUNTRY_OF_MANUFACTURE = 'country_of_manufacture';
    const ECCN = 'eccn';
    const SCHEDULE_B_INFO = 'schedule_b_info';
    const HS_CODE = 'hs_code';

    const MANUFACTURE = 'manufacture';
    const UNIT = 'unit';
    const ORIGIN = 'origin';
    const MONETARY_VALUE = 'monetary_value';
    const CLASSIFICATION_CODES = 'classification_codes';

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return int
     */
    public function getQty();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getCategoryName();

    /**
     * @return string
     */
    public function getDimensionsUom();

    /**
     * @return float
     */
    public function getLength();

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @return string
     */
    public function getWeightUom();

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @return bool
     */
    public function isFragile();

    /**
     * @return bool
     */
    public function isVirtual();

    /**
     * @return bool
     */
    public function isPrePackaged();

    /**
     * @return bool
     */
    public function canRotateVertically();

    /**
     * @return string
     */
    public function getCountryOfOrigin();

    /**
     * @return string
     */
    public function getCountryOfManufacture();

    /**
     * @return string
     */
    public function getEccn();

    /**
     * @return string
     */
    public function getScheduleBinfo();

    /**
     * @return string
     */
    public function getHsCode();

    /**
     * @return string
     */
    public function getMonetaryValue();
}
