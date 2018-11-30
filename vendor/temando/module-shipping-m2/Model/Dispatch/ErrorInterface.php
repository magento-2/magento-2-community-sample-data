<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Dispatch;

/**
 * Temando Dispatch Shipment Interface.
 *
 * @deprecated since 1.3.0
 * @see \Temando\Shipping\Model\Shipment\ShipmentErrorInterface
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ErrorInterface
{
    const TITLE = 'title';
    const DETAIL = 'detail';

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDetail();
}
