<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Carrier Configurations Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetCarrierConfigurations implements GetCarrierConfigurationsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\CarrierConfigurationResponseType[]
     */
    private $data = [];

    /**
     * Obtain response entities
     *
     * @return \Temando\Shipping\Rest\Response\Type\CarrierConfigurationResponseType[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entities
     *
     * @param \Temando\Shipping\Rest\Response\Type\CarrierConfigurationResponseType[] $data
     *
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
