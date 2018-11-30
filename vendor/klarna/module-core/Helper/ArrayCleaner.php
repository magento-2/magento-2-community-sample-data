<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Helper;

/**
 * Class ArrayCleaner
 *
 * @package Klarna\Core\Helper
 */
class ArrayCleaner
{
    /**
     * Remove duplicate items from a multidimensional array based on a supplied key
     *
     * @param array  $array
     * @param string $key
     * @return array
     */
    public function removeDuplicates(array $array, $key = 'id')
    {
        /** @noinspection CallableInLoopTerminationConditionInspection */
        // The count statement is intentional as the array's size will decrease
        for ($parent_index = 0; $parent_index < count($array); $parent_index++) {
            $duplicate = null;
            /** @noinspection CallableInLoopTerminationConditionInspection */
            // The count statement is intentional as the array's size will decrease
            for ($child_index = $parent_index + 1; $child_index < count($array); $child_index++) {
                if (strcmp($array[$child_index][$key], $array[$parent_index][$key]) === 0) {
                    $duplicate = $child_index;
                    break;
                }
            }
            if (null !== $duplicate) {
                array_splice($array, $duplicate, 1);
            }
        }
        return $array;
    }
}
