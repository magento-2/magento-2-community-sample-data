<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

/**
 * Temando Shipment Error Interface.
 *
 * Error as occurred during allocation, dispatch, or batch processes.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ShipmentErrorInterface
{
    const STATUS = 'status';
    const TITLE  = 'title';
    const CODE   = 'code';
    const DETAIL = 'detail';

    /**
     * Get error status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Get error title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get error code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get error detail (optional)
     *
     * @return string
     */
    public function getDetail();
}
