<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Update Order Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class UpdateOrder implements UpdateOrderInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Order
     */
    private $order;

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Order
     */
    public function getData()
    {
        return $this->order;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Order $order
     * @return void
     */
    public function setData(\Temando\Shipping\Rest\Response\DataObject\Order $order)
    {
        $this->order = $order;
    }
}
