<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Generic;

/**
 * Temando API Remaining Space Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class RemainingSpace
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    private $volume;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Generic\Value
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Generic\Value $volume
     * @return void
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    }
}
