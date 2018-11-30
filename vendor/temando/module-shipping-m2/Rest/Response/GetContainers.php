<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Containers Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetContainers implements GetContainersInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\ContainerResponseType[]
     */
    private $data = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Type\ContainerResponseType[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $containers \Temando\Shipping\Rest\Response\Type\ContainerResponseType[]
     * @return void
     */
    public function setData(array $containers)
    {
        $this->data = $containers;
    }
}
