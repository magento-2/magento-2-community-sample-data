<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\ApiClient;

use Magento\Framework\Exception\LocalizedException;

/**
 * Converts PHP objects into an associative array
 */
class ObjectConverter
{
    /**
     * Convert an object to an associative array
     *
     * @param object $object
     * @return array
     * @throws LocalizedException
     */
    public function convertToArray($object)
    {
        $result = json_decode(json_encode($object), true);
        if ($result === false || $result === null) {
            throw new LocalizedException(__('Could not convert object to array'));
        }
        return $result;
    }
}
