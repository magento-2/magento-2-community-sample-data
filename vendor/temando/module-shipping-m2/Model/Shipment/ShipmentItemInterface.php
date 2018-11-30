<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Shipment Item Interface.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com
 */
interface ShipmentItemInterface
{
    const QTY = 'qty';
    const SKU = 'sku';

    /**
     * @return int
     */
    public function getQty();

    /**
     * @return string
     */
    public function getSku();
}
