<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Plugin\View;

use \Magento\Framework\View\Asset\Minification;

/**
 * MinificationPlugin
 *
 * @package  Temando\Shipping\Plugin
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class MinificationPlugin
{
    /**
     * Exclude static componentry files from being minified.
     *
     * Using the config node `minify_exclude` is not an option because it does
     * not get merged but overridden by subsequent modules.
     *
     * @see \Magento\Framework\View\Asset\Minification::XML_PATH_MINIFICATION_EXCLUDES
     *
     * @param Minification $subject
     * @param string[] $excludes
     * @param string $contentType
     * @return string[]
     */
    public function afterGetExcludes(Minification $subject, array $excludes, $contentType)
    {
        if ($contentType !== 'js') {
            return $excludes;
        }

        $excludes[]= '/Temando_Shipping/static/js/';
        return $excludes;
    }
}
