<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterfaceFactory;
use Temando\Shipping\Model\CollectionPoint\OrderCollectionPoint;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\OrderCollectionPoint as CollectionPointResource;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;

/**
 * Temando Order Collection Point Repository
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class OrderCollectionPointRepository implements OrderCollectionPointRepositoryInterface
{
    /**
     * @var CollectionPointResource
     */
    private $resource;

    /**
     * @var OrderCollectionPointInterfaceFactory
     */
    private $collectionPointFactory;

    /**
     * CollectionPointRepository constructor.
     * @param CollectionPointResource $resource
     * @param OrderCollectionPointInterfaceFactory $collectionPointFactory
     */
    public function __construct(
        CollectionPointResource $resource,
        OrderCollectionPointInterfaceFactory $collectionPointFactory
    ) {
        $this->resource = $resource;
        $this->collectionPointFactory = $collectionPointFactory;
    }

    /**
     * Load collection point by shipping address id.
     *
     * @param int $addressId
     * @return OrderCollectionPointInterface
     * @throws NoSuchEntityException
     */
    public function get($addressId)
    {
        /** @var OrderCollectionPoint $collectionPoint */
        $collectionPoint = $this->collectionPointFactory->create();

        try {
            $this->resource->load($collectionPoint, $addressId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Collection point with id "%1" does not exist.', $addressId));
        }

        if (!$collectionPoint->getRecipientAddressId()) {
            throw new NoSuchEntityException(__('Collection point with id "%1" does not exist.', $addressId));
        }

        return $collectionPoint;
    }

    /**
     * @param OrderCollectionPointInterface $collectionPoint
     * @return OrderCollectionPointInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderCollectionPointInterface $collectionPoint)
    {
        try {
            /** @var OrderCollectionPoint $collectionPoint */
            $this->resource->save($collectionPoint);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save collection point.'), $exception);
        }

        return $collectionPoint;
    }
}
