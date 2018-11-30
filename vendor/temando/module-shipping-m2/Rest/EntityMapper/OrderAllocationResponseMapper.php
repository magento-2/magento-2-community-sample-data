<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\Shipment\AllocationErrorInterface;
use Temando\Shipping\Model\Shipment\AllocationErrorInterfaceFactory;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Rest\Response\Type\ShipmentResponseType;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderAllocationResponseMapper
{
    /**
     * @var AllocationErrorInterfaceFactory
     */
    private $allocationErrorFactory;

    /**
     * @var ShipmentResponseMapper
     */
    private $shipmentResponseMapper;

    /**
     * AllocationMapper constructor.
     * @param AllocationErrorInterfaceFactory $allocationErrorFactory
     * @param ShipmentResponseMapper $shipmentResponseMapper
     */
    public function __construct(
        AllocationErrorInterfaceFactory $allocationErrorFactory,
        ShipmentResponseMapper $shipmentResponseMapper
    ) {
        $this->allocationErrorFactory = $allocationErrorFactory;
        $this->shipmentResponseMapper = $shipmentResponseMapper;
    }

    /**
     * @param ShipmentResponseType[] $allocateIncluded
     * @return AllocationErrorInterface[]
     */
    public function mapErrors(array $allocateIncluded)
    {
        /** @var ShipmentResponseType[] $includedErrors */
        $includedErrors = array_filter($allocateIncluded, function (ShipmentResponseType $element) {
            return ($element->getType() == 'error');
        });

        $allocationErrors = [];
        foreach ($includedErrors as $item) {
            $allocationError = $this->allocationErrorFactory->create(['data' => [
                AllocationErrorInterface::TITLE => $item->getAttributes()->getTitle(),
                AllocationErrorInterface::CODE => $item->getAttributes()->getCode(),
                AllocationErrorInterface::STATUS => $item->getAttributes()->getStatus(),
                AllocationErrorInterface::DETAIL => $item->getAttributes()->getDetail(),
            ]]);

            $allocationErrors[]= $allocationError;
        }

        return $allocationErrors;
    }

    /**
     * @param ShipmentResponseType[] $allocateIncluded
     * @return ShipmentInterface[]
     */
    public function mapShipments(array $allocateIncluded)
    {
        /** @var ShipmentResponseType[] $includedShipments */
        $includedShipments = array_filter($allocateIncluded, function (ShipmentResponseType $element) {
            return ($element->getType() == 'shipment');
        });

        $shipments = [];
        foreach ($includedShipments as $shipment) {
            $shipments[]= $this->shipmentResponseMapper->map($shipment);
        }

        return $shipments;
    }
}
