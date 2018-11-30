<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Location;

/**
 * Temando API Location Opening Hours Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OpeningHours
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\DefaultOpeningHours[]
     */
    private $default = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions
     */
    private $exceptions;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\DefaultOpeningHours[]
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\DefaultOpeningHours[] $default
     * @return void
     */
    public function setDefault(array $default)
    {
        $this->default = $default;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions $exceptions
     */
    public function setExceptions($exceptions)
    {
        $this->exceptions = $exceptions;
    }
}
