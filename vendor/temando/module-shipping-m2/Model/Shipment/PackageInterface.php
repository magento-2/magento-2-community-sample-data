<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Package Interface.
 *
 * The package data object represents one part of the shipment packages list.
 *
 * @package Temando\Shipping\Model
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com
 */
interface PackageInterface
{
    const PACKAGE_ID = 'id';
    const TRACKING_REFERENCE = 'tracking_reference';
    const WEIGHT = 'gross_weight_value';
    const LENGTH = 'dimensions_length';
    const WIDTH = 'dimensions_width';
    const HEIGHT = 'dimensions_height';
    const ITEMS = 'items';

    /**
     * @return string
     */
    public function getPackageId();

    /**
     * @return string
     */
    public function getTrackingReference();

    /**
     * @return float
     */
    public function getWeight();

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
     * @return PackageItemInterface[]
     */
    public function getItems();
}
