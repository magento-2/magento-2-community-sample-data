<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Shipment Item Interface.
 *
 * The package data object represents one part of the shipment packages list.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com
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
