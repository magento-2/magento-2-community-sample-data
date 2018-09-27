<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Temando\Shipping\Api\CollectionPoint\CartCollectionPointManagementInterface;

/**
 * Manage Collection Point Searches
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
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
     * @return \Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface
     * @throws CouldNotSaveException
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
     */
    public function deleteSearchRequest($cartId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->collectionPointManagement->deleteSearchRequest($shippingAddress->getId());
    }

    /**
     * @param int $cartId
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface[]
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
     */
    public function selectCollectionPoint($cartId, $entityId)
    {
        $shippingAddress = $this->addressManagement->get($cartId);

        return $this->collectionPointManagement->selectCollectionPoint($shippingAddress->getId(), $entityId);
    }
}
