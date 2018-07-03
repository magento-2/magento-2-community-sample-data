<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Vertex\Tax\Model\Config;
use Magento\Tax\Model\System\Config\Source\Algorithm;

/**
 * Adds Vertex to the Tax Calculation algorithms
 *
 * @see Algorithm
 * @deprecated Vertex will be removed as a calculation method in the future, as this is incompatible with fallbacks
 */
class AlgorithmPlugin
{
    /**
     * Add "Vertex" as a method for Tax Calculation to be based on to the list of calculation algorithms
     *
     * MEQP2 Warning: Unused Parameter $subject necessary for plugins
     *
     * @see Algorithm::toOptionArray()
     *
     * @param Algorithm $subject
     * @param array $options
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject is a necessary part of a plugin
     */
    public function afterToOptionArray(Algorithm $subject, $options)
    {
        $option = [
            'value' => Config::CALC_UNIT_VERTEX,
            'label' => __('Vertex'),
        ];

        $options[] = $option;

        return $options;
    }
}
