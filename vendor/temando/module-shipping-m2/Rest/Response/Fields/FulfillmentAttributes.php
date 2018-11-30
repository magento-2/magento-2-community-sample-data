<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

/**
 * Temando API Fulfillment Resource Object Attributes
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class FulfillmentAttributes
{
    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $readyAt;

    /**
     * @var string
     */
    private $cancelledAt;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Fulfillment\Item[]
     */
    private $items = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\LocationAttributes
     */
    private $pickUpLocation;

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
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
     * @return string
     */
    public function getCancelledAt()
    {
        return $this->cancelledAt;
    }

    /**
     * @param string $cancelledAt
     * @return void
     */
    public function setCancelledAt($cancelledAt)
    {
        $this->cancelledAt = $cancelledAt;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     * @return void
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\LocationAttributes
     */
    public function getPickUpLocation()
    {
        return $this->pickUpLocation;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\LocationAttributes $pickUpLocation
     * @return void
     */
    public function setPickUpLocation(LocationAttributes $pickUpLocation)
    {
        $this->pickUpLocation = $pickUpLocation;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Fulfillment\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Fulfillment\Item[] $items
     * @return void
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }
}
