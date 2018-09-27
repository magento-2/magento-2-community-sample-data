<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Create Order Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CreateOrderInterface
{
    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\Type\OrderResponseType
     */
    public function getData();

    /**
     * Set response entity
     *
     * @param \Temando\Shipping\Rest\Response\Type\OrderResponseType $order
     * @return void
     */
    public function setData(\Temando\Shipping\Rest\Response\Type\OrderResponseType $order);

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\Type\OrderIncludedResponseType[]
     */
    public function getIncluded();

    /**
     * Set response entity
     *
     * @param \Temando\Shipping\Rest\Response\Type\OrderIncludedResponseType[] $included
     * @return void
     */
    public function setIncluded($included);
}
