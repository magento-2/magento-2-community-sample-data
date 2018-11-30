<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Pickup\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use Temando\Shipping\Model\PickupInterface;

/**
 * Temando Pickup State Option Source
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class StateOptionSource implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => PickupInterface::STATE_REQUESTED,
                'label' => __('Pickup Requested'),
            ],
            [
                'value' => PickupInterface::STATE_READY,
                'label' => __('Ready for Pickup'),
            ],
            [
                'value' => PickupInterface::STATE_PICKED_UP,
                'label' => __('Picked Up'),
            ],
            [
                'value' => PickupInterface::STATE_CANCELLED,
                'label' => __('Cancelled'),
            ],
        ];

        return $options;
    }
}
