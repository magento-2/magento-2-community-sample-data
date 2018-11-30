<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface as SalesOrderInterface;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterface;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterfaceFactory;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Collection Point Manifestation Processor.
 *
 * Assign the collection point selected during checkout to an order address.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class CollectionPointManifestProcessor implements SaveProcessorInterface
{
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var OrderCollectionPointInterfaceFactory
     */
    private $collectionPointFactory;

    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * CollectionPointManifestProcessor constructor.
     * @param HydratorInterface $hydrator
     * @param OrderCollectionPointInterfaceFactory $collectionPointFactory
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     */
    public function __construct(
        HydratorInterface $hydrator,
        OrderCollectionPointInterfaceFactory $collectionPointFactory,
        OrderCollectionPointRepositoryInterface $collectionPointRepository
    ) {
        $this->hydrator = $hydrator;
        $this->collectionPointFactory = $collectionPointFactory;
        $this->collectionPointRepository = $collectionPointRepository;
    }

    /**
     * Assign order shipping address to the selected collection point.
     *
     * @param SalesOrderInterface|\Magento\Sales\Model\Order $salesOrder
     * @param OrderInterface $requestType
     * @param OrderResponseTypeInterface $responseType
     * @return void
     * @throws LocalizedException
     */
    public function postProcess(
        SalesOrderInterface $salesOrder,
        OrderInterface $requestType,
        OrderResponseTypeInterface $responseType
    ) {
        /** @var \Temando\Shipping\Model\CollectionPoint\QuoteCollectionPoint $collectionPoint */
        $quoteCollectionPoint = $requestType->getCollectionPoint();
        if ($quoteCollectionPoint->getEntityId()) {
            $shippingAddressId = $salesOrder->getShippingAddress()->getId();

            $collectionPointData = $this->hydrator->extract($quoteCollectionPoint);
            $collectionPointData[OrderCollectionPointInterface::RECIPIENT_ADDRESS_ID] = $shippingAddressId;

            $orderCollectionPoint = $this->collectionPointFactory->create(['data' => $collectionPointData]);

            $this->collectionPointRepository->save($orderCollectionPoint);
        }
    }
}
