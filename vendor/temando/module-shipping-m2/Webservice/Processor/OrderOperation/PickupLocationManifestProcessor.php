<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface as SalesOrderInterface;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterface;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterfaceFactory;
use Temando\Shipping\Api\Data\Delivery\QuotePickupLocationInterface;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderPickupLocationRepositoryInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Pickup Location Manifestation Processor.
 *
 * Assign the pickup location selected during checkout to an order address.
 *
 * @package Temando\Shipping\Webservice
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupLocationManifestProcessor implements SaveProcessorInterface
{
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var OrderPickupLocationInterfaceFactory
     */
    private $pickupLocationFactory;

    /**
     * @var OrderPickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * CollectionPointManifestProcessor constructor.
     * @param HydratorInterface $hydrator
     * @param OrderPickupLocationInterfaceFactory $pickupLocationFactory
     * @param OrderPickupLocationRepositoryInterface $pickupLocationRepository
     */
    public function __construct(
        HydratorInterface $hydrator,
        OrderPickupLocationInterfaceFactory $pickupLocationFactory,
        OrderPickupLocationRepositoryInterface $pickupLocationRepository
    ) {
        $this->hydrator = $hydrator;
        $this->pickupLocationFactory = $pickupLocationFactory;
        $this->pickupLocationRepository = $pickupLocationRepository;
    }

    /**
     * Assign order shipping address to the selected pickup location.
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
        /** @var \Temando\Shipping\Model\Delivery\QuotePickupLocation $pickupLocation */
        $quotePickupLocation = $requestType->getPickupLocation();
        if ($quotePickupLocation instanceof QuotePickupLocationInterface) {
            $shippingAddressId = $salesOrder->getShippingAddress()->getId();

            $pickupLocationData = $this->hydrator->extract($quotePickupLocation);
            $pickupLocationData[OrderPickupLocationInterface::RECIPIENT_ADDRESS_ID] = $shippingAddressId;

            $orderPickupLocation = $this->pickupLocationFactory->create(['data' => $pickupLocationData]);

            $this->pickupLocationRepository->save($orderPickupLocation);
        }
    }
}
