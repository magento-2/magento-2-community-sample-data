<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Response\Fields\Fulfillment\Item;

/**
 * Temando API Fulfillment Item Product Field
 *
 * @package Temando\Shipping\Rest
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Product
{
    /**
     * @var string
     */
    private $sku;

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return void
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }
}
