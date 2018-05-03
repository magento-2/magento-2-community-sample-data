<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment;

use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\ExportDeclaration;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\SelectedServices;

/**
 * Temando API Shipment Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Attributes
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\ExportDeclaration
     */
    private $exportDeclaration;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\SelectedServices
     */
    private $selectedServices;

    /**
     * Anonymous list of capabilities.
     *
     * $format = [
     *   'capabilityCodeX' => [
     *     'propertyOne' => 'valueOne',
     *     'propertyTwo' => 'valueTwo',
     *   ],
     *   'capabilityCodeY' => [
     *     'propertyOne' => 'valueOne',
     *   ],
     * ]
     *
     * @var mixed[][]
     */
    private $capabilities = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill
     */
    private $fulfill;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $modifiedAt;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin
     */
    private $origin;

    /**
     * @var bool
     */
    private $isDutiable;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Documentation[]
     */
    private $documentation = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination
     */
    private $destination;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package[]
     */
    private $packages = [];

    /**
     * @var string
     */
    private $pickupAt;

    /**
     * @var string
     */
    private $originId;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var bool
     */
    private $isPaperless;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\ExportDeclaration
     */
    public function getExportDeclaration()
    {
        return $this->exportDeclaration;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\ExportDeclaration $exportDeclaration
     */
    public function setExportDeclaration(ExportDeclaration $exportDeclaration)
    {
        $this->exportDeclaration = $exportDeclaration;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\SelectedServices
     */
    public function getSelectedServices()
    {
        return $this->selectedServices;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\SelectedServices $selectedServices
     */
    public function setSelectedServices(SelectedServices $selectedServices)
    {
        $this->selectedServices = $selectedServices;
    }

    /**
     * @return mixed[][]
     */
    public function getCapabilities()
    {
        return $this->capabilities;
    }

    /**
     * @param mixed[][] $capabilities
     */
    public function setCapabilities(array $capabilities)
    {
        $this->capabilities = $capabilities;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill
     */
    public function getFulfill()
    {
        return $this->fulfill;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill $fulfill
     */
    public function setFulfill($fulfill)
    {
        $this->fulfill = $fulfill;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin $origin
     */
    public function setOrigin(Origin $origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return boolean
     */
    public function isDutiable()
    {
        return $this->isDutiable;
    }

    /**
     * @param boolean $isDutiable
     */
    public function setIsDutiable($isDutiable)
    {
        $this->isDutiable = $isDutiable;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Documentation[]
     */
    public function getDocumentation()
    {
        return $this->documentation;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Documentation[] $documentation
     */
    public function setDocumentation($documentation)
    {
        $this->documentation = $documentation;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination $destination
     */
    public function setDestination(Destination $destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return Attributes\Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param Attributes\Package[] $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return string
     */
    public function getPickupAt()
    {
        return $this->pickupAt;
    }

    /**
     * @param string $pickupAt
     */
    public function setPickupAt($pickupAt)
    {
        $this->pickupAt = $pickupAt;
    }

    /**
     * @return string
     */
    public function getOriginId()
    {
        return $this->originId;
    }

    /**
     * @param string $originId
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return boolean
     */
    public function getIsPaperless()
    {
        return $this->isPaperless;
    }

    /**
     * @param boolean $isPaperless
     */
    public function setIsPaperless($isPaperless)
    {
        $this->isPaperless = $isPaperless;
    }
}
