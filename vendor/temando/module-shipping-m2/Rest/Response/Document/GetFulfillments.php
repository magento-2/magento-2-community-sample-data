<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Fulfillment Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetFulfillments implements GetFulfillmentsInterface
{

    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Fulfillment[]
     */
    private $data = [];

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\Fulfillment[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entity
     *
     * @param \Temando\Shipping\Rest\Response\DataObject\Fulfillment[] $fulfillments
     * @return void
     */
    public function setData(array $fulfillments)
    {
        $this->data = $fulfillments;
    }
}
