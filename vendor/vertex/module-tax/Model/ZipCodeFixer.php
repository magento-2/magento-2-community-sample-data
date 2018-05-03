<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

/**
 * Fixer class for fixing user errors related to postal codes
 *
 * 4 digit zipcodes to 5 digit zipcodes prefixed with a 0
 */
class ZipCodeFixer
{
    /**
     * Corrects erroneously entered US ZIP or ZIP+4 codes where 0s have been omitted from the beginning
     *
     * @param string $postcode A US ZIP or ZIP+4 postcode
     * @return string Same ZIP code prepended with 0's if length of first part is less than 5
     */
    public function fix($postcode)
    {
        if ($postcode === null) {
            return null;
        }
        $parts = explode('-', $postcode);
        $parts[0] = str_pad($parts[0], 5, '0', STR_PAD_LEFT);
        return implode('-', $parts);
    }
}
