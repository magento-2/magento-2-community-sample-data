<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Batch;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order\Shipment;
use Temando\Shipping\Api\Data\Delivery\OrderCollectionPointInterface;
use Temando\Shipping\Model\BatchInterface;
use Temando\Shipping\Model\BatchProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;
use Temando\Shipping\ViewModel\DataProvider\BatchUrl;
use Temando\Shipping\ViewModel\DataProvider\OrderAddress as AddressRenderer;
use Temando\Shipping\ViewModel\DataProvider\OrderDate;
use Temando\Shipping\ViewModel\DataProvider\OrderUrl;

/**
 * View model for batch details page.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class BatchDetails implements ArgumentInterface
{
    /**
     * @var BatchProviderInterface
     */
    private $batchProvider;

    /**
     * @var OrderDate
     */
    private $orderDate;

    /**
     * @var BatchUrl
     */
    private $batchUrl;

    /**
     * @var OrderUrl
     */
    private $orderUrl;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var OrderAddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * BatchDetails constructor.
     * @param BatchProviderInterface $batchProvider
     * @param OrderDate $orderDate
     * @param BatchUrl $batchUrl
     * @param OrderUrl $orderUrl
     * @param DataObjectFactory $dataObjectFactory
     * @param AddressRenderer $addressRenderer
     * @param OrderAddressInterfaceFactory $orderAddressInterfaceFactory
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        BatchProviderInterface $batchProvider,
        OrderDate $orderDate,
        BatchUrl $batchUrl,
        OrderUrl $orderUrl,
        DataObjectFactory $dataObjectFactory,
        AddressRenderer $addressRenderer,
        OrderAddressInterfaceFactory $orderAddressInterfaceFactory,
        OrderCollectionPointRepositoryInterface $collectionPointRepository,
        RegionFactory $regionFactory
    ) {
        $this->batchProvider = $batchProvider;
        $this->orderDate = $orderDate;
        $this->batchUrl = $batchUrl;
        $this->orderUrl = $orderUrl;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->addressRenderer = $addressRenderer;
        $this->addressFactory = $orderAddressInterfaceFactory;
        $this->collectionPointRepository = $collectionPointRepository;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    public function getBatch()
    {
        /** @var \Temando\Shipping\Model\Batch $batch */
        $batch = $this->batchProvider->getBatch();
        return $batch;
    }

    /**
     * @param string $date
     * @return \DateTime
     */
    public function getDate(string $date): \DateTime
    {
        return $this->orderDate->getDate($date);
    }

    /**
     * Obtain batch create url
     *
     * @return string
     */
    public function getNewActionUrl(): string
    {
        return $this->batchUrl->getNewActionUrl();
    }

    /**
     * Obtain batch listing url
     *
     * @return string
     */
    public function getListActionUrl(): string
    {
        return $this->batchUrl->getListActionUrl();
    }

    /**
     * Obtain url for troubleshooting failed batches
     *
     * @return string
     */
    public function getSolveUrl(): string
    {
        return $this->batchUrl->getSolveActionUrl([
            BatchInterface::BATCH_ID => $this->batchProvider->getBatch()->getBatchId(),
        ]);
    }

    /**
     * @param string $orderId
     * @return string
     */
    public function getOrderViewUrl(string $orderId): string
    {
        return $this->orderUrl->getViewActionUrl(['order_id' => $orderId]);
    }

    /**
     * @param $extShipmentId
     * @return  \Magento\Sales\Api\Data\OrderInterface | null
     */
    private function getSalesShipmentOrder($extShipmentId)
    {
        $orders = $this->batchProvider->getOrders();
        if (isset($orders[$extShipmentId])) {
            return $orders[$extShipmentId];
        }

        return null;
    }

    /**
     * Returns ShipmentInfo based on extShipmentId.
     *
     * fixme(nr): replace by some proper piece of software
     *
     * @param string $extShipmentId
     * @return \Magento\Framework\DataObject
     */
    public function getShipmentInfoForGrid($extShipmentId)
    {
        $info = [];

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getSalesShipmentOrder($extShipmentId);

        if ($order) {
            /** @var Shipment $shipment */
            $shipment = $order->getShipmentsCollection()->getFirstItem();
            $shippingAddress = $shipment->getShippingAddress();
            $shippingAddressId = $shippingAddress->getId();

            try {
                /** @var OrderCollectionPointInterface $collectionPoint */
                $collectionPoint = $this->collectionPointRepository->get($shippingAddressId);

                /** @var \Magento\Directory\Model\Region $region */
                $region = $this->regionFactory->create();
                $region = $region->loadByCode($collectionPoint->getRegion(), $collectionPoint->getCountry());
                $regionName = $region->getName();

                $addressData = [
                    'company'    => $collectionPoint->getName(),
                    'street'     => $collectionPoint->getStreet(),
                    'region'     => $regionName,
                    'city'       => $collectionPoint->getCity(),
                    'postcode'   => $collectionPoint->getPostcode(),
                    'country_id' => $collectionPoint->getCountry(),

                ];
                $shippingAddress = $this->addressFactory->create($addressData);
            } catch (LocalizedException $e) {
                $shippingAddress = $shipment->getShippingAddress();
            }

            $formattedShippingAddress = $this->addressRenderer->getFormattedAddress($shippingAddress);
            $itemsInfo = $this->getItemsOrderedInfo($shipment->getItems());
            $info = [
                'shipment_id' => $shipment->getId(),
                'ship_to_name' => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                'destination_address' => $formattedShippingAddress,
                'items_ordered' => $itemsInfo,
            ];
        }

        $shipmentInfo = $this->dataObjectFactory->create(['data' => $info]);

        return $shipmentInfo;
    }

    /**
     * @param ShipmentItemInterface[] $items
     * @return string
     */
    private function getItemsOrderedInfo($items)
    {
        $info = '';
        foreach ($items as $item) {
            $info .= $item->getName() . ' x ' . (int) $item->getQty() . '<br>';
        }

        return $info;
    }
}
