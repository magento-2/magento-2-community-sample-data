<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Completion;

/**
 * Temando API Completion Attributes Response Type
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
    private $status;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $readyAt;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Shipment[]
     */
    private $shipments = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Group[]
     */
    private $groups = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Callback
     */
    private $callback;

    /**
     * @var int
     */
    private $totalShipments;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Completion\Attributes\CustomAttributes
     */
    private $customAttributes;

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return void
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
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getReadyAt()
    {
        return $this->readyAt;
    }

    /**
     * @param string $readyAt
     * @return void
     */
    public function setReadyAt($readyAt)
    {
        $this->readyAt = $readyAt;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Shipment[]
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Shipment[] $shipments
     * @return void
     */
    public function setShipments(array $shipments)
    {
        $this->shipments = $shipments;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Group[] $groups
     * @return void
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Callback
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Callback $callback
     * @return void
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return int
     */
    public function getTotalShipments()
    {
        return $this->totalShipments;
    }

    /**
     * @param int $totalShipments
     * @return void
     */
    public function setTotalShipments($totalShipments)
    {
        $this->totalShipments = $totalShipments;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Completion\Attributes\CustomAttributes
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @param Attributes\CustomAttributes $customAttributes
     * @return void
     */
    public function setCustomAttributes($customAttributes)
    {
        $this->customAttributes = $customAttributes;
    }
}
