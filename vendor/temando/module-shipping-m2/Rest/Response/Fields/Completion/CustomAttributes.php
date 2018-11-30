<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Completion;

/**
 * Temando API Completion Custom Attributes Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CustomAttributes
{
    /**
     * @var string
     */
    private $carrierName;

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrierName;
    }

    /**
     * @param string $carrierName
     * @return void
     */
    public function setCarrierName($carrierName)
    {
        $this->carrierName = $carrierName;
    }
}
