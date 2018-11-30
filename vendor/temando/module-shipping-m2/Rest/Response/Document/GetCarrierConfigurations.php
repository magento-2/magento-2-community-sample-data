<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Carrier Configuration Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetCarrierConfigurations implements GetCarrierConfigurationsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\CarrierConfiguration[]
     */
    private $data = [];

    /**
     * Obtain response entities
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\CarrierConfiguration[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entities
     *
     * @param \Temando\Shipping\Rest\Response\DataObject\CarrierConfiguration[] $configurations
     *
     * @return void
     */
    public function setData(array $configurations)
    {
        $this->data = $configurations;
    }
}
