<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Api\Data\Checkout;

/**
 * Checkout shipping address extension interface
 *
 * @api
 * @package  Temando\Shipping\Api
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface AddressInterface
{
    const ENTITY_ID = 'entity_id';
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const SERVICE_SELECTION = 'service_selection';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return void
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getShippingAddressId();

    /**
     * @param int $addressId
     * @return void
     */
    public function setShippingAddressId($addressId);

    /**
     * @return \Magento\Framework\Api\AttributeInterface[]
     */
    public function getServiceSelection();

    /**
     * @param \Magento\Framework\Api\AttributeInterface[] $services
     * @return void
     */
    public function setServiceSelection(array $services);
}
