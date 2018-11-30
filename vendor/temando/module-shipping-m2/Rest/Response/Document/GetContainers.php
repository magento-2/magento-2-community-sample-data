<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Container Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetContainers implements GetContainersInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Container[]
     */
    private $data = [];

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Container[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $containers \Temando\Shipping\Rest\Response\DataObject\Container[]
     * @return void
     */
    public function setData(array $containers)
    {
        $this->data = $containers;
    }
}
