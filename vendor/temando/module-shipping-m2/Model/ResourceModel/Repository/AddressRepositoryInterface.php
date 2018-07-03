<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

/**
 * Temando Checkout Address Repository Interface.
 *
 * A checkout address entity is an extension to the quote shipping address. It
 * holds additional data needed for rates processing and order manifestation.
 *
 * This public interface can be used to retrieve and write additional address
 * data as collected during checkout.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface AddressRepositoryInterface
{
    /**
     * Load address by entity id.
     *
     * @param string $addressId
     * @return \Temando\Shipping\Api\Data\Checkout\AddressInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($addressId);

    /**
     * Load address by quote address id.
     *
     * @param string $quoteAddressId
     * @return \Temando\Shipping\Api\Data\Checkout\AddressInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQuoteAddressId($quoteAddressId);

    /**
     * Save address.
     *
     * @param \Temando\Shipping\Api\Data\Checkout\AddressInterface $address
     * @return \Temando\Shipping\Api\Data\Checkout\AddressInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Temando\Shipping\Api\Data\Checkout\AddressInterface $address);

    /**
     * Delete by quote address id.
     *
     * @param string $addressId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteByShippingAddressId($addressId);

    /**
     * Delete entity.
     *
     * @param \Temando\Shipping\Api\Data\Checkout\AddressInterface $address
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Temando\Shipping\Api\Data\Checkout\AddressInterface $address);
}
