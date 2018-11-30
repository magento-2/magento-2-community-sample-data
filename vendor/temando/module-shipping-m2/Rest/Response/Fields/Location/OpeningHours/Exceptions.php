<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Location\OpeningHours;

/**
 * Temando API Location Opening Hours Exceptions Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Exceptions
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions\OpeningHoursException[]
     */
    private $closures = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions\OpeningHoursException[]
     */
    private $openings = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions\OpeningHoursException[]
     */
    public function getClosures()
    {
        return $this->closures;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions\OpeningHoursException[] $closures
     * @return void
     */
    public function setClosures(array $closures)
    {
        $this->closures = $closures;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions\OpeningHoursException[]
     */
    public function getOpenings()
    {
        return $this->openings;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours\Exceptions\OpeningHoursException[] $openings
     * @return void
     */
    public function setOpenings(array $openings)
    {
        $this->openings = $openings;
    }
}
