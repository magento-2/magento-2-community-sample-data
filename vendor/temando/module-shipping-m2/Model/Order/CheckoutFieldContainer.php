<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Framework\DataObject;

/**
 * Temando Order Checkout Field Container.
 *
 * This container is mainly introduced for consistency reasons. All order
 * subtypes are created through a simple type builder, so be the checkout fields.
 *
 * @see \Temando\Shipping\Model\Order\CheckoutFieldContainerInterfaceBuilder
 * @see \Temando\Shipping\Model\OrderInterfaceBuilder::setRateRequest
 * @see \Temando\Shipping\Model\OrderInterfaceBuilder::setOrder
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CheckoutFieldContainer extends DataObject implements CheckoutFieldContainerInterface
{
    /**
     * Obtain checkout fields for further processing during order placement.
     *
     * @return \Temando\Shipping\Model\Checkout\Attribute\CheckoutFieldInterface[]
     */
    public function getFields()
    {
        return $this->getData(self::FIELDS);
    }
}
