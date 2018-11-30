<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

use Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue;
use Temando\Shipping\Rest\Response\Fields\Order\Customer;
use Temando\Shipping\Rest\Response\Fields\Order\DeliverTo;
use Temando\Shipping\Rest\Response\Fields\Order\Source;

/**
 * Temando API Order Resource Object Attributes
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderAttributes
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Order\Fee[]
     */
    private $fees = [];

    /**
     * @var string
     */
    private $lastModifiedAt;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    private $totalPaid;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Order\Source
     */
    private $source;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Package[]
     */
    private $packages;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo
     */
    private $deliverTo;

    /**
     * @var string
     */
    private $orderedAt;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    private $total;

    /**
     * @var string
     */
    private $originId;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Item[]
     */
    private $items = [];

    /**
     * @var string
     */
    private $status;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Order\Customer
     */
    private $customer;

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
    public function getLastModifiedAt()
    {
        return $this->lastModifiedAt;
    }

    /**
     * @param string $lastModifiedAt
     * @return void
     */
    public function setLastModifiedAt($lastModifiedAt)
    {
        $this->lastModifiedAt = $lastModifiedAt;
    }

    /**
     * @return string
     */
    public function getOrderedAt()
    {
        return $this->orderedAt;
    }

    /**
     * @param string $orderedAt
     * @return void
     */
    public function setOrderedAt($orderedAt)
    {
        $this->orderedAt = $orderedAt;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Order\Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Order\Source $source
     * @return void
     */
    public function setSource(Source $source)
    {
        $this->source = $source;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Order\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Order\Customer $customer
     * @return void
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo
     */
    public function getDeliverTo()
    {
        return $this->deliverTo;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo $deliverTo
     * @return void
     */
    public function setDeliverTo(DeliverTo $deliverTo)
    {
        $this->deliverTo = $deliverTo;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Item[] $items
     * @return void
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue $total
     * @return void
     */
    public function setTotal(MonetaryValue $total)
    {
        $this->total = $total;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Order\Fee[]
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Order\Fee[] $fees
     */
    public function setFees(array $fees)
    {
        $this->fees = $fees;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue
     */
    public function getTotalPaid()
    {
        return $this->totalPaid;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\MonetaryValue $totalPaid
     */
    public function setTotalPaid(MonetaryValue $totalPaid)
    {
        $this->totalPaid = $totalPaid;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Package[] $packages
     */
    public function setPackages(array $packages)
    {
        $this->packages = $packages;
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
}
