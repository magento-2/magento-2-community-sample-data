<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type;

/**
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AttributeFilter
{
    /**
     * Remove all empty items from request data attributes
     *
     * @param mixed[] $attributes
     * @return mixed[]
     */
    public static function notEmpty(array $attributes)
    {
        foreach ($attributes as &$attribute) {
            if (is_array($attribute)) {
                $attribute = self::notEmpty($attribute);
            }
        }

        $filteredAttributes = array_filter($attributes, function ($item) {
            if ($item instanceof EmptyFilterableInterface) {
                return !$item->isEmpty();
            } elseif ($item === null) {
                // skip null values
                return false;
            } elseif ($item === '') {
                // skip empty strings
                return false;
            } elseif (is_array($item) && empty($item)) {
                // skip empty arrays
                return false;
            }

            // non-empty values can pass; zero and boolean false can also pass
            return true;
        });

        return $filteredAttributes;
    }
}
