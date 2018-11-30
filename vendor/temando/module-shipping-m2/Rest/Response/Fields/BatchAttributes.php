<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

/**
 * Temando API Batch Resource Object Attributes
 *
 * @package  Temando\Shipping\Rest
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class BatchAttributes
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
    private $modifiedAt;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Batch\Shipment[]
     */
    private $shipments = [];

    /**
     * @var int
     */
    private $totalShipments;

    /**
     * @var string
     */
    private $documentation;

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
     * @param string $modifiedAt
     * @return void
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Batch\Shipment[]
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Batch\Shipment[] $shipments
     * @return void
     */
    public function setShipments(array $shipments)
    {
        $this->shipments = $shipments;
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
     * @return string
     */
    public function getDocumentation()
    {
        return $this->documentation;
    }

    /**
     * @param string $documentation
     * @return void
     */
    public function setDocumentation($documentation)
    {
        $this->documentation = $documentation;
    }
}
