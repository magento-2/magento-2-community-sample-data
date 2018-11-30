<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Rma;

use Magento\Framework\Exception\CouldNotSaveException;
use Temando\Shipping\Api\Rma\RmaShipmentManagementInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Repository\RmaShipmentRepositoryInterface;

/**
 * Manage RMA Shipments
 *
 * @package Temando\Shipping\Api
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class RmaShipmentManagement implements RmaShipmentManagementInterface
{
    /**
     * @var RmaShipmentRepositoryInterface
     */
    private $rmaShipmentRepository;

    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * RmaShipmentManagement constructor.
     *
     * @param RmaShipmentRepositoryInterface $rmaShipmentRepository
     * @param ModuleConfigInterface $moduleConfig
     */
    public function __construct(
        RmaShipmentRepositoryInterface $rmaShipmentRepository,
        ModuleConfigInterface $moduleConfig
    ) {
        $this->rmaShipmentRepository = $rmaShipmentRepository;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Assign platform shipment IDs to a core RMA entity.
     *
     * @param int $rmaId
     * @param string[] $returnShipmentIds
     *
     * @return int Number of successfully assigned shipment IDs.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignShipmentIds($rmaId, array $returnShipmentIds)
    {
        if (!$this->moduleConfig->isRmaAvailable()) {
            throw new CouldNotSaveException(__('RMA is not available'));
        }

        return $this->rmaShipmentRepository->saveShipmentIds($rmaId, $returnShipmentIds);
    }
}
