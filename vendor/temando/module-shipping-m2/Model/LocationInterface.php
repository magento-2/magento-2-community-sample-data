<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Location Interface.
 *
 * The location data object represents one item in the origin location grid listing.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface LocationInterface
{
    const LOCATION_ID = 'location_id';
    const NAME = 'name';
    const UNIQUE_IDENTIFIER = 'unique_identifier';
    const TYPE = 'type';
    const STREET = 'street';
    const POSTAL_CODE = 'postal_code';
    const IS_DEFAULT = 'is_default';

    /**
     * @return string
     */
    public function getLocationId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getUniqueIdentifier();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string[]
     */
    public function getStreet();

    /**
     * @return string
     */
    public function getPostalCode();

    /**
     * @return bool
     */
    public function isDefault();
}
