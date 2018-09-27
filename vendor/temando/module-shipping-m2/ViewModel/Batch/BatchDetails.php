<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Batch;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterfaceFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Sales\Model\Order\Shipment;
use Temando\Shipping\Model\BatchInterface;
use Temando\Shipping\Model\BatchProviderInterface;
use Temando\Shipping\ViewModel\DataProvider\BatchUrl;

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
     * @var TimezoneInterfaceFactory
     */
    private $timezoneFactory;

    /**
     * @var BatchUrl
     */
    private $batchUrl;

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
     * BatchDetails constructor.
     * @param BatchProviderInterface $batchProvider
     * @param TimezoneInterfaceFactory $timezoneFactory
     * @param BatchUrl $batchUrl
     * @param DataObjectFactory $dataObjectFactory
     * @param AddressRenderer $addressRenderer
     * @param OrderAddressInterfaceFactory $orderAddressInterfaceFactory
     */
    public function __construct(
        BatchProviderInterface $batchProvider,
        TimezoneInterfaceFactory $timezoneFactory,
        BatchUrl $batchUrl,
        DataObjectFactory $dataObjectFactory,
        AddressRenderer $addressRenderer,
        OrderAddressInterfaceFactory $orderAddressInterfaceFactory
    ) {
        $this->batchProvider = $batchProvider;
        $this->timezoneFactory = $timezoneFactory;
        $this->batchUrl = $batchUrl;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->addressRenderer = $addressRenderer;
        $this->addressFactory = $orderAddressInterfaceFactory;
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
     * Obtain date. Parent method fails to convert date format returned from api.
     *
     * @see formatDate()
     * @see Timezone::formatDateTime()
     *
     * @param string $date
     * @return \DateTime
     */
    public function getDate($date)
    {
        $timezone = $this->timezoneFactory->create();
        $localizedDate = $timezone->date(new \DateTime($date));

        return $localizedDate;
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
     * @param string $extShipmentId
     * @return \Magento\Framework\DataObject
     */
    public function getShipmentInfoForGrid($extShipmentId)
    {

        $info = [];
        $order = $this->getSalesShipmentOrder($extShipmentId);
        if ($order) {
            /** @var Shipment $shipment */
            $shipment = $order->getShipmentsCollection()->getFirstItem();
            /** @var Address $shippingAddress */
            $shippingAddress = $shipment->getShippingAddress();
            $formattedShippingAddress = $this->getFormattedAddress($shipment->getShippingAddress());
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

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    private function getFormattedAddress(Address $address)
    {
        $addressData = [
            'street'     => $address->getStreet(),
            'region'     => $address->getRegion(),
            'city'       => $address->getCity(),
            'postcode'   => $address->getPostcode(),
            'country_id' => $address->getCountryId(),
        ];

        /** @var \Magento\Sales\Model\Order\Address $address */
        $address = $this->addressFactory->create(['data' => $addressData]);
        return $this->addressRenderer->format($address, 'html');
    }
}
