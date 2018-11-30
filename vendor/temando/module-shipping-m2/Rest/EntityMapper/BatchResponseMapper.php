<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\EntityMapper;

use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Model\Order\Address\Renderer;
use Temando\Shipping\Model\BatchInterface;
use Temando\Shipping\Model\BatchInterfaceFactory;
use Temando\Shipping\Model\Shipment\ShipmentErrorInterface;
use Temando\Shipping\Model\Shipment\ShipmentErrorInterfaceFactory;
use Temando\Shipping\Model\Shipment\ShipmentSummaryInterface;
use Temando\Shipping\Model\Shipment\ShipmentSummaryInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\Batch;
use Temando\Shipping\Rest\Response\DataObject\Shipment;
use Temando\Shipping\Rest\Response\Fields\Batch\Shipment as ShipmentReference;
use Temando\Shipping\Rest\Response\Fields\LocationAttributes;

/**
 * Map API data to application data object
 *
 * @package Temando\Shipping\Rest
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class BatchResponseMapper
{
    /**
     * @var BatchInterfaceFactory
     *
     */
    private $batchFactory;

    /**
     * @var ShipmentSummaryInterfaceFactory
     */
    private $shipmentFactory;

    /**
     * @var ShipmentErrorInterfaceFactory
     */
    private $errorFactory;

    /**
     * @var OrderAddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var Renderer
     */
    private $addressRenderer;

    /**
     * DispatchResponseMapper constructor.
     *
     * @param BatchInterfaceFactory $batchFactory
     * @param ShipmentSummaryInterfaceFactory $shipmentFactory
     * @param ShipmentErrorInterfaceFactory $errorFactory
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param Renderer $addressRenderer
     */
    public function __construct(
        BatchInterfaceFactory $batchFactory,
        ShipmentSummaryInterfaceFactory $shipmentFactory,
        ShipmentErrorInterfaceFactory $errorFactory,
        OrderAddressInterfaceFactory $addressFactory,
        Renderer $addressRenderer
    ) {
        $this->batchFactory = $batchFactory;
        $this->shipmentFactory = $shipmentFactory;
        $this->errorFactory = $errorFactory;
        $this->addressFactory = $addressFactory;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @param LocationAttributes $location
     *
     * @return string
     */
    private function getFormattedDestinationAddress(LocationAttributes $location)
    {
        $addressData = [
            'region'     => $location->getAddress()->getAdministrativeArea(),
            'postcode'   => $location->getAddress()->getPostalCode(),
            'street'     => implode(' ', $location->getAddress()->getLines()),
            'city'       => $location->getAddress()->getLocality(),
            'country_id' => $location->getAddress()->getCountryCode(),
            'company'    => $location->getContact()->getOrganisationName()
        ];
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address $address */
        $address          = $this->addressFactory->create(['data' => $addressData]);
        $formattedAddress = $this->addressRenderer->format($address, 'html');

        return (string)$formattedAddress;
    }

    /**
     * @param ShipmentReference $apiShipmentReference
     * @param Shipment $apiShipment
     *
     * @return ShipmentSummaryInterface
     */
    private function mapShipment(ShipmentReference $apiShipmentReference, Shipment $apiShipment)
    {
        $errors = [];
        foreach ($apiShipmentReference->getErrors() as $apiError) {
            $errors[]= $this->errorFactory->create(['data' => [
                ShipmentErrorInterface::TITLE => $apiError->getTitle(),
                ShipmentErrorInterface::DETAIL => $apiError->getDetail(),
            ]]);
        }

        $destination = $apiShipment->getAttributes()->getDestination();
        // fixme(nr): this is wrong! mappers do not prepare for display. pass on full location instead
        $address = $this->getFormattedDestinationAddress($destination);
        $recipientName = [
            $destination->getContact()->getPersonFirstName(),
            $destination->getContact()->getPersonLastName(),
        ];

        // fixme(nr): fatal error, shipment.order is not a required field
        $shipment = $this->shipmentFactory->create(['data' => [
            ShipmentSummaryInterface::ORDER_ID => $apiShipment->getAttributes()->getOrder()->getReference(),
            ShipmentSummaryInterface::SHIPMENT_ID => $apiShipment->getId(),
            ShipmentSummaryInterface::STATUS => $apiShipment->getAttributes()->getStatus(),
            ShipmentSummaryInterface::ERRORS => $errors,
            ShipmentSummaryInterface::RECIPIENT_ADDRESS => $address,
            ShipmentSummaryInterface::RECIPIENT_NAME => implode(' ', $recipientName),
        ]]);

        return $shipment;
    }

    /**
     * @param Batch $apiBatch
     *
     * @return BatchInterface
     */
    public function map(Batch $apiBatch)
    {
        $batchId = $apiBatch->getId();
        $status = $apiBatch->getAttributes()->getStatus();
        $createdAtDate = $apiBatch->getAttributes()->getCreatedAt();
        $updatedAtDate = $apiBatch->getAttributes()->getModifiedAt();
        $failedShipments = [];
        $includedShipments = [];
        $documentation = $apiBatch->getAttributes()->getDocumentation();

        $shipments = [];
        foreach ($apiBatch->getShipments() as $shipment) {
            $shipments[$shipment->getId()] = $shipment;
        }

        // split shipments into failed and successfully created
        foreach ($apiBatch->getAttributes()->getShipments() as $shipmentReference) {
            $mappedShipment = $this->mapShipment($shipmentReference, $shipments[$shipmentReference->getId()]);
            if ($shipmentReference->getStatus() === 'error') {
                $failedShipments[$shipmentReference->getId()] = $mappedShipment;
            } else {
                $includedShipments[$shipmentReference->getId()] = $mappedShipment;
            }
        }

        $batch = $this->batchFactory->create(['data' => [
            BatchInterface::BATCH_ID => $batchId,
            BatchInterface::STATUS => $status,
            BatchInterface::CREATED_AT_DATE => $createdAtDate,
            BatchInterface::UPDATED_AT_DATE => $updatedAtDate,
            BatchInterface::INCLUDED_SHIPMENTS => $includedShipments,
            BatchInterface::FAILED_SHIPMENTS => $failedShipments,
            BatchInterface::DOCUMENTATION => $documentation,
        ]]);

        return $batch;
    }
}
