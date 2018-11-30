<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Response\Document;

use Temando\Shipping\Rest\Response\DataObject\Batch;

/**
 * Temando API Get Batch Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetBatch implements GetBatchInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Batch
     */
    private $data;

    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Shipment[]
     */
    private $included;

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\Batch
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entity
     *
     * @param \Temando\Shipping\Rest\Response\DataObject\Batch $batch
     *
     * @return void
     */
    public function setData(Batch $batch)
    {
        $this->data = $batch;
    }

    /**
     * Obtain included affecting shipments
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\Shipment[]
     */
    public function getIncluded()
    {
        return $this->included;
    }

    /**
     * Set included affecting shipments
     *
     * @param \Temando\Shipping\Rest\Response\DataObject\Shipment[] $included
     *
     * @return void
     */
    public function setIncluded(array $included)
    {
        $this->included = $included;
    }
}
