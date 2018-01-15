<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment;

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
     * @var string
     */
    private $pickupAt;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $originId;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill
     */
    private $fulfill;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Documentation[]
     */
    private $documentation = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin
     */
    private $origin;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination
     */
    private $destination;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package[]
     */
    private $packages = [];

    /**
     * @var bool
     */
    private $isPaperless;

    /**
     * @return string
     */
    public function getPickupAt()
    {
        return $this->pickupAt;
    }

    /**
     * @param string $pickupAt
     * @return void
     */
    public function setPickupAt($pickupAt)
    {
        $this->pickupAt = $pickupAt;
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
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
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
     * @return void
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;
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
     * @return void
     */
    public function setFulfill(\Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Fulfill $fulfill)
    {
        $this->fulfill = $fulfill;
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
     * @return void
     */
    public function setDocumentation(array $documentation)
    {
        $this->documentation = $documentation;
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
     * @return void
     */
    public function setOrigin(\Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin $origin)
    {
        $this->origin = $origin;
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
     * @return void
     */
    public function setDestination(\Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Destination $destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package[] $packages
     * @return void
     */
    public function setPackages(array $packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return bool
     */
    public function getIsPaperless()
    {
        return $this->isPaperless;
    }

    /**
     * @param bool $isPaperless
     * @return void
     */
    public function setIsPaperless($isPaperless)
    {
        $this->isPaperless = $isPaperless;
    }
}
