<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Location Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetLocation implements GetLocationInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\LocationResponseType
     */
    private $data;

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\Type\LocationResponseType
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entity
     *
     * @param  \Temando\Shipping\Rest\Response\Type\LocationResponseType $location
     * @return void
     */
    public function setData(\Temando\Shipping\Rest\Response\Type\LocationResponseType $location)
    {
        $this->data = $location;
    }
}
