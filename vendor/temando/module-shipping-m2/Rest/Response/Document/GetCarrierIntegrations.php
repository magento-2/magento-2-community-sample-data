<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Carrier Integration Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetCarrierIntegrations implements GetCarrierIntegrationsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\CarrierIntegration[]
     */
    private $data = [];

    /**
     * Obtain response entities
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\CarrierIntegration[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entities
     *
     * @param \Temando\Shipping\Rest\Response\DataObject\CarrierIntegration[] $integrations
     *
     * @return void
     */
    public function setData(array $integrations)
    {
        $this->data = $integrations;
    }
}
