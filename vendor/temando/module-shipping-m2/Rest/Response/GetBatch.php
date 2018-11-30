<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Response;

use Temando\Shipping\Rest\Response\Type\BatchResponseType;

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
     * @var \Temando\Shipping\Rest\Response\Type\BatchResponseType
     */
    private $data;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\ShipmentResponseType[]
     */
    private $included;

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\Type\BatchResponseType
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entity
     *
     * @param \Temando\Shipping\Rest\Response\Type\BatchResponseType $batch
     *
     * @return void
     */
    public function setData(BatchResponseType $batch)
    {
        $this->data = $batch;
    }

    /**
     * Obtain included affecting shipments
     *
     * @return \Temando\Shipping\Rest\Response\Type\ShipmentResponseType[]
     */
    public function getIncluded()
    {
        return $this->included;
    }

    /**
     * Set included affecting shipments
     *
     * @param \Temando\Shipping\Rest\Response\Type\ShipmentResponseType[] $included
     *
     * @return void
     */
    public function setIncluded(array $included)
    {
        $this->included = $included;
    }
}
