<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location;

/**
 * Temando API Order Included  CollectionPoint OpeningHours Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OpeningHours
{
    /**
     * @codingStandardsIgnoreLine
     * @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location\OpeningHours\DefaultOpeningHours[]
     */
    private $default;

    /**
     * @codingStandardsIgnoreLine
     * @return \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location\OpeningHours\DefaultOpeningHours[]
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Location\OpeningHours\DefaultOpeningHours[] $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }
}
