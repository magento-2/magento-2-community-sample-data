<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando RMA Shipment Repository Interface.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface RmaShipmentRepositoryInterface
{
    /**
     * @param SearchCriteriaInterface $criteria
     * @return ShipmentInterface[]
     */
    public function getAddedShipments(SearchCriteriaInterface $criteria);

    /**
     * @param SearchCriteriaInterface $criteria
     * @return ShipmentInterface[]
     */
    public function getAvailableShipments(SearchCriteriaInterface $criteria);

    /**
     * Save external shipment IDs to be associated with core RMA entity.
     *
     * @param int $rmaId
     * @param string[] $shipmentIds
     * @return int Number of saved shipments
     * @throws CouldNotSaveException
     */
    public function saveShipmentIds($rmaId, array $shipmentIds);

    /**
     * Revoke assignment of external shipment IDs with core RMA entity.
     *
     * @param int $rmaId
     * @param string[] $shipmentIds
     * @return int Number of saved shipments
     * @throws CouldNotDeleteException
     */
    public function deleteShipmentIds($rmaId, array $shipmentIds);
}
