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
class ExtensibleTypeProcessor
{
    /**
     * @param mixed[] $subject
     * @param ExtensibleTypeAttribute $attribute
     * @return mixed[]
     */
    public static function addAttribute(array $subject, ExtensibleTypeAttribute $attribute)
    {
        $path = $attribute->getDataPath();
        $currentLevel = array_shift($path);

        if (empty($path)) {
            // done. finally set value.
            $subject[$currentLevel] = $attribute->getValue();
            return $subject;
        }

        // update path for upcoming recursion
        $attribute->setDataPath($path);

        if (!isset($subject[$currentLevel])) {
            // not available: create
            $subject[$currentLevel] = [];
        }

        if ($subject[$currentLevel] instanceof ExtensibleTypeInterface) {
            // pass in attribute definition to descendant.
            /** @var ExtensibleTypeInterface $requestType */
            $requestType = $subject[$currentLevel];
            $requestType->addAdditionalAttribute($attribute);
            return $subject;
        } else {
            // proceed into next recursion
            $subject[$currentLevel] = static::addAttribute($subject[$currentLevel], $attribute);
            return $subject;
        }
    }
}
