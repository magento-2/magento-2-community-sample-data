<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\CollectionPoint;

/**
 * Temando Collection Point Search Request Interface
 *
 * @api
 * @package  Temando\Shipping\Api
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface SearchRequestInterface
{
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const COUNTRY_ID = 'country_id';
    const POSTCODE = 'postcode';
    const PENDING = 'pending';

    /**
     * @return int
     */
    public function getShippingAddressId();

    /**
     * @return string
     */
    public function getCountryId();

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @return bool
     */
    public function isPending();
}
