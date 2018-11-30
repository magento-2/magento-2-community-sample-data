<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Location\OpeningHours;

/**
 * Temando API Location Default Opening Hours Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class DefaultOpeningHours
{
    /**
     * @var string
     */
    private $dayOfWeek;

    /**
     * @var string
     */
    private $opens;

    /**
     * @var string
     */
    private $closes;

    /**
     * @return string
     */
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * @param string $dayOfWeek
     * @return void
     */
    public function setDayOfWeek($dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * @return string
     */
    public function getOpens()
    {
        return $this->opens;
    }

    /**
     * @param string $opens
     * @return void
     */
    public function setOpens($opens)
    {
        $this->opens = $opens;
    }

    /**
     * @return string
     */
    public function getCloses()
    {
        return $this->closes;
    }

    /**
     * @param string $closes
     * @return void
     */
    public function setCloses($closes)
    {
        $this->closes = $closes;
    }
}
