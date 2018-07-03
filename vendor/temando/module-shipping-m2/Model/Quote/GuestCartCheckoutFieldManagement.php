<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Quote;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\GuestCart\GuestShippingAddressManagementInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterfaceFactory;
use Temando\Shipping\Api\Quote\GuestCartCheckoutFieldManagementInterface;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;

/**
 * Manage Checkout Fields
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class GuestCartCheckoutFieldManagement implements GuestCartCheckoutFieldManagementInterface
{
    /**
     * @var GuestShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * GuestCartCheckoutFieldManagement constructor.
     * @param GuestShippingAddressManagementInterface $addressManagement
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressFactory
     */
    public function __construct(
        GuestShippingAddressManagementInterface $addressManagement,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressFactory
    ) {
        $this->addressManagement = $addressManagement;
        $this->addressRepository = $addressRepository;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @param string $cartId
     * @param AttributeInterface[] $serviceSelection
     * @return void
     * @throws CouldNotSaveException
     */
    public function saveCheckoutFields($cartId, $serviceSelection)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        try {
            $address = $this->addressRepository->getByQuoteAddressId($shippingAddress->getId());
        } catch (NoSuchEntityException $e) {
            $address = $this->addressFactory->create(['data' => [
                AddressInterface::SHIPPING_ADDRESS_ID => $shippingAddress->getId()
            ]]);
        }

        $address->setServiceSelection($serviceSelection);
        $this->addressRepository->save($address);
    }
}
