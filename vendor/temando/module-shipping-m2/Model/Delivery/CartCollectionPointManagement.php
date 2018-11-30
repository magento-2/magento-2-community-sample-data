<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Temando\Shipping\Api\Data\Delivery\CollectionPointSearchRequestInterface;
use Temando\Shipping\Api\Data\Delivery\QuoteCollectionPointInterface;
use Temando\Shipping\Api\Delivery\CartCollectionPointManagementInterface;

/**
 * Manage Collection Point Searches
 *
 * @package Temando\Shipping\Model
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CartCollectionPointManagement implements CartCollectionPointManagementInterface
{
    /**
     * @var ShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var CollectionPointManagement
     */
    private $collectionPointManagement;

    /**
     * CartCollectionPointManagement constructor.
     *
     * @param ShippingAddressManagementInterface $addressManagement
     * @param CollectionPointManagement $collectionPointManagement
     */
    public function __construct(
        ShippingAddressManagementInterface $addressManagement,
        CollectionPointManagement $collectionPointManagement
    ) {
        $this->addressManagement = $addressManagement;
        $this->collectionPointManagement = $collectionPointManagement;
    }

    /**
     * @param int $cartId
     * @param string $countryId
     * @param string $postcode
     * @return CollectionPointSearchRequestInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveSearchRequest($cartId, $countryId, $postcode)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->collectionPointManagement->saveSearchRequest($shippingAddress->getId(), $countryId, $postcode);
    }

    /**
     * @param int $cartId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteSearchRequest($cartId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->collectionPointManagement->deleteSearchRequest($shippingAddress->getId());
    }

    /**
     * @param int $cartId
     * @return QuoteCollectionPointInterface[]
     * @throws NoSuchEntityException
     */
    public function getCollectionPoints($cartId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->collectionPointManagement->getCollectionPoints($shippingAddress->getId());
    }

    /**
     * @param int $cartId
     * @param int $entityId
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function selectCollectionPoint($cartId, $entityId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->collectionPointManagement->selectCollectionPoint($shippingAddress->getId(), $entityId);
    }
}
