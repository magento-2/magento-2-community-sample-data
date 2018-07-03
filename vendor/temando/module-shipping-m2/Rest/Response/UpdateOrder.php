<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Update Order Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class UpdateOrder implements UpdateOrderInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\OrderResponseType
     */
    private $order;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\OrderIncludedResponseType[]
     */
    private $included;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\OrderResponseType
     */
    public function getData()
    {
        return $this->order;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\OrderResponseType $order
     * @return void
     */
    public function setData(Type\OrderResponseType $order)
    {
        $this->order = $order;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\OrderIncludedResponseType[]
     */
    public function getIncluded()
    {
        return $this->included;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\OrderIncludedResponseType[] $included
     * @return void
     */
    public function setIncluded($included)
    {
        $this->included = $included;
    }
}
