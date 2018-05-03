<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Api\Rma;

/**
 * Manage RMA Shipments
 *
 * @api
 * @package  Temando\Shipping\Api
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface RmaShipmentManagementInterface
{
    /**
     * Assign platform shipment IDs to a core RMA entity.
     *
     * @param int $rmaId
     * @param string[] $returnShipmentIds
     *
     * @return int Number of successfully assigned shipment IDs.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignShipmentIds($rmaId, array $returnShipmentIds);
}
