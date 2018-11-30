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
use Temando\Shipping\Rest\Response\GetBatch;
use Temando\Shipping\Rest\Response\Type\Batch\Attributes\Shipment;
use Temando\Shipping\Rest\Response\Type\Generic\Location;
use Temando\Shipping\Rest\Response\Type\ShipmentResponseType;

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
    private $shipmentSummaryFactory;

    /**
     * @var ShipmentErrorInterfaceFactory
     */
    private $shipmentErrorFactory;

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
     * @param BatchInterfaceFactory           $batchFactory
     * @param ShipmentSummaryInterfaceFactory $shipmentSummaryFactory
     * @param ShipmentErrorInterfaceFactory   $shipmentErrorFactory
     * @param OrderAddressInterfaceFactory    $addressFactory
     * @param Renderer                        $addressRenderer
     */
    public function __construct(
        BatchInterfaceFactory $batchFactory,
        ShipmentSummaryInterfaceFactory $shipmentSummaryFactory,
        ShipmentErrorInterfaceFactory $shipmentErrorFactory,
        OrderAddressInterfaceFactory $addressFactory,
        Renderer $addressRenderer
    ) {
        $this->batchFactory           = $batchFactory;
        $this->shipmentSummaryFactory = $shipmentSummaryFactory;
        $this->shipmentErrorFactory   = $shipmentErrorFactory;
        $this->addressFactory         = $addressFactory;
        $this->addressRenderer        = $addressRenderer;
    }

    /**
     * @param Location $location
     *
     * @return string
     */
    private function getFormattedDestinationAddress(Location $location)
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
     * @param Shipment               $apiShipment
     * @param ShipmentResponseType[] $includedShipments
     *
     * @return ShipmentSummaryInterface
     */
    private function mapShipment(Shipment $apiShipment, array $includedShipments)
    {
        $errors = [];
        $currentErrors = $apiShipment->getErrors() ? $apiShipment->getErrors() : [];
        foreach ($currentErrors as $apiError) {
            $errors[] = $this->shipmentErrorFactory->create(['data' =>
                    [
                        ShipmentErrorInterface::TITLE  => $apiError->getTitle(),
                        ShipmentErrorInterface::DETAIL => $apiError->getDetail(),
                    ]
            ]);
        }

        $shipmentAttributes = $includedShipments[$apiShipment->getId()]->getAttributes();
        $recipientData      = $shipmentAttributes->getDestination();
        $shipment = $this->shipmentSummaryFactory->create(['data' =>
            [
                ShipmentSummaryInterface::ORDER_ID => $shipmentAttributes->getOrder()->getReference(),
                ShipmentSummaryInterface::SHIPMENT_ID => $apiShipment->getId(),
                ShipmentSummaryInterface::STATUS => $shipmentAttributes->getStatus(),
                ShipmentSummaryInterface::ERRORS => $errors,
                ShipmentSummaryInterface::RECIPIENT_ADDRESS => $this->getFormattedDestinationAddress($recipientData),
                ShipmentSummaryInterface::RECIPIENT_NAME => $recipientData->getContact()->getPersonFirstName() . ' '
                    . $recipientData->getContact()->getPersonLastName(),
            ]
        ]);

        return $shipment;
    }

    /**
     * @param GetBatch $apiBatch
     *
     * @return BatchInterface
     */
    public function map(GetBatch $apiBatch)
    {
        $batch                  = $apiBatch->getData();
        $batchIncludedShipments = [];
        /** @var ShipmentResponseType $includedShipment */
        foreach ($apiBatch->getIncluded() as $includedShipment) {
            $batchIncludedShipments[$includedShipment->getId()] = $includedShipment;
        }

        $batchId         = $batch->getId();
        $status          = $batch->getAttributes()->getStatus();
        $createdAtDate   = $batch->getAttributes()->getCreatedAt();
        $updatedAtDate   = $batch->getAttributes()->getModifiedAt();
        $failedShipments = $includedShipments = [];
        $documentation   = $batch->getAttributes()->getDocumentation();

        // split shipments into failed and successfully booked
        foreach ($batch->getAttributes()->getShipments() as $apiShipment) {
            if ($apiShipment->getStatus() === 'error') {
                $failedShipments[$apiShipment->getId()] = $this->mapShipment($apiShipment, $batchIncludedShipments);
            } else {
                $includedShipments[$apiShipment->getId()] = $this->mapShipment($apiShipment, $batchIncludedShipments);
            }
        }

        $batch = $this->batchFactory->create(['data' =>
            [
                BatchInterface::BATCH_ID => $batchId,
                BatchInterface::STATUS => $status,
                BatchInterface::CREATED_AT_DATE => $createdAtDate,
                BatchInterface::UPDATED_AT_DATE => $updatedAtDate,
                BatchInterface::INCLUDED_SHIPMENTS => $includedShipments,
                BatchInterface::FAILED_SHIPMENTS => $failedShipments,
                BatchInterface::DOCUMENTATION => $documentation,
            ]
        ]);

        return $batch;
    }
}
