<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Magento\Framework\Exception\LocalizedException;

/**
 * Map application data object properties to API data JSON pointer
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface PointerAwareInterface
{
    /**
     * Obtain the JSON pointer for a data model.
     *
     * @param string $property
     * @return string
     * @throws LocalizedException
     */
    public function getPath($property);
}
