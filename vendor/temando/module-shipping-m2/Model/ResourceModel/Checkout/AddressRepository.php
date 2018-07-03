<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Checkout;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Checkout\Address as AddressResource;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;

/**
 * Temando Shipment Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AddressRepository implements AddressRepositoryInterface
{
    /**
     * @var AddressResource
     */
    private $resource;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * AddressRepository constructor.
     * @param Address $resource
     * @param AddressInterfaceFactory $addressFactory
     */
    public function __construct(
        Address $resource,
        AddressInterfaceFactory $addressFactory
    ) {
        $this->resource = $resource;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @param int $addressId
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    public function getById($addressId)
    {
        /** @var \Temando\Shipping\Model\Checkout\Address $address */
        $address = $this->addressFactory->create();
        $this->resource->load($address, $addressId);

        if (!$address->getId()) {
            throw new NoSuchEntityException(__('Address with id "%1" does not exist.', $addressId));
        }

        return $address;
    }

    /**
     * @param int $quoteAddressId
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    public function getByQuoteAddressId($quoteAddressId)
    {
        $addressId = $this->resource->getIdByQuoteAddressId($quoteAddressId);
        return $this->getById($addressId);
    }

    /**
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws CouldNotSaveException
     */
    public function save(AddressInterface $address)
    {
        try {
            /** @var \Temando\Shipping\Model\Checkout\Address $address */
            $this->resource->save($address);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save shipping services.'), $exception);
        }
        return $address;
    }

    /**
     * @param string $addressId
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function deleteByShippingAddressId($addressId)
    {
        $addressId = $this->resource->getIdByQuoteAddressId($addressId);
        /** @var \Temando\Shipping\Model\Checkout\Address $address */
        $address = $this->addressFactory->create(['data' => [
            AddressInterface::ENTITY_ID => $addressId
        ]]);

        return $this->delete($address);
    }

    /**
     * @param AddressInterface $address
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(AddressInterface $address)
    {
        try {
            /** @var \Temando\Shipping\Model\Checkout\Address $address */
            $this->resource->delete($address);
        } catch (\Exception $exception) {
            $msg = __('Shipping services for address "%1" could not be deleted.', $address->getEntityId());
            throw new CouldNotDeleteException($msg);
        }

        return true;
    }
}
