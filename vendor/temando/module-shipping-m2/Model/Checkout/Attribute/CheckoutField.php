<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\Attribute;

use Magento\Framework\DataObject;

/**
 * Temando Checkout Field
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CheckoutField extends DataObject implements CheckoutFieldInterface
{
    /**
     * Obtain checkout field attribute code.
     *
     * @return string
     */
    public function getFieldId()
    {
        return $this->getData(self::FIELD_ID);
    }

    /**
     * Obtain checkout field value as selected during checkout.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * Obtain json query path for checkout field in order request.
     *
     * @return string
     */
    public function getOrderPath()
    {
        return $this->getData(self::ORDER_PATH);
    }
}
