<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Temando\Shipping\Api\CollectionPoint\GuestCartCollectionPointManagementInterface as LegacyManagementInterface;
use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterfaceFactory as LegacyCollectionPointFactory;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterfaceFactory as LegacySearchRequestFactory;
use Temando\Shipping\Api\Delivery\GuestCartCollectionPointManagementInterface;

/**
 * Manage Collection Point Searches
 *
 * @deprecated since 1.4.0
 * @see \Temando\Shipping\Model\Delivery\GuestCartCollectionPointManagement
 *
 * @package Temando\Shipping\Model
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GuestCartCollectionPointManagement implements LegacyManagementInterface
{
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var LegacySearchRequestFactory
     */
    private $searchRequestFactory;

    /**
     * @var LegacyCollectionPointFactory
     */
    private $collectionPointFactory;

    /**
     * @var GuestCartCollectionPointManagementInterface
     */
    private $collectionPointManagement;

    /**
     * GuestCartCollectionPointManagement constructor.
     *
     * @param HydratorInterface $hydrator
     * @param LegacySearchRequestFactory $searchRequestFactory
     * @param LegacyCollectionPointFactory $collectionPointFactory
     * @param GuestCartCollectionPointManagementInterface $collectionPointManagement
     */
    public function __construct(
        HydratorInterface $hydrator,
        LegacySearchRequestFactory $searchRequestFactory,
        LegacyCollectionPointFactory $collectionPointFactory,
        GuestCartCollectionPointManagementInterface $collectionPointManagement
    ) {
        $this->hydrator = $hydrator;
        $this->searchRequestFactory = $searchRequestFactory;
        $this->collectionPointFactory = $collectionPointFactory;
        $this->collectionPointManagement = $collectionPointManagement;
    }

    /**
     * @param string $cartId
     * @param string $countryId
     * @param string $postcode
     * @return \Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function saveSearchRequest($cartId, $countryId, $postcode)
    {
        $legacySearchRequest = $this->searchRequestFactory->create();

        $searchRequest = $this->collectionPointManagement->saveSearchRequest($cartId, $countryId, $postcode);
        $searchRequestData = $this->hydrator->extract($searchRequest);
        $this->hydrator->hydrate($legacySearchRequest, $searchRequestData);

        return $legacySearchRequest;
    }

    /**
     * @param string $cartId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteSearchRequest($cartId)
    {
        return $this->collectionPointManagement->deleteSearchRequest($cartId);
    }

    /**
     * @param string $cartId
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface[]
     */
    public function getCollectionPoints($cartId)
    {
        $legacyCollectionPoints = [];

        $collectionPoints =  $this->collectionPointManagement->getCollectionPoints($cartId);

        foreach ($collectionPoints as $collectionPoint) {
            $legacyCollectionPoint = $this->collectionPointFactory->create();
            $collectionPointData = $this->hydrator->extract($collectionPoint);
            $this->hydrator->hydrate($legacyCollectionPoint, $collectionPointData);

            $legacyCollectionPoints[]= $legacyCollectionPoint;
        }

        return $legacyCollectionPoints;
    }

    /**
     * @param string $cartId
     * @param int $entityId
     * @return bool
     * @throws CouldNotSaveException
     */
    public function selectCollectionPoint($cartId, $entityId)
    {
        return $this->collectionPointManagement->selectCollectionPoint($cartId, $entityId);
    }
}
