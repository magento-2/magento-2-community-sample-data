<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Packaging Interface.
 *
 * The packaging/container data object represents one item in the packaging grid listing.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface PackagingInterface
{
    const PACKAGING_ID = 'packaging_id';
    const NAME = 'name';
    const TYPE = 'type';
    const WIDTH = 'width';
    const LENGTH = 'length';
    const HEIGHT = 'height';
    const TARE_WEIGHT = 'tare_weight';
    const MAX_WEIGHT = 'max_weight';

    /**
     * @return string
     */
    public function getPackagingId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string[]
     */
    public function getWidth();

    /**
     * @return string
     */
    public function getLength();

    /**
     * @return string
     */
    public function getHeight();

    /**
     * @return string
     */
    public function getTareWeight();

    /**
     * @return string
     */
    public function getMaxWeight();
}
