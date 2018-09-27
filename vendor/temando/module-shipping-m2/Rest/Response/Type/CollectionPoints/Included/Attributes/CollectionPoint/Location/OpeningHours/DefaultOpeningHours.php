<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
// @codingStandardsIgnoreLine
namespace Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location\OpeningHours;

/**
 * Temando API Order Included  CollectionPoint OpeningHours Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
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
     */
    public function setCloses($closes)
    {
        $this->closes = $closes;
    }
}
