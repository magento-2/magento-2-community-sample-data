<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\Attribute;

/**
 * Temando Checkout Field Interface
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CheckoutFieldInterface
{
    const FIELD_ID = 'field_id';
    const VALUE = 'value';
    const ORDER_PATH = 'order_path';

    /**
     * Obtain checkout field attribute code.
     *
     * @return mixed
     */
    public function getFieldId();

    /**
     * Obtain checkout field value as selected during checkout.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Obtain json query path for checkout field in order request.
     *
     * @return string
     */
    public function getOrderPath();
}
