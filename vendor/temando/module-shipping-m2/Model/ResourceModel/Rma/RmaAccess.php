<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\Rma;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Rma\Api\RmaRepositoryInterface;
use Magento\Rma\Model\ResourceModel\Item as RmaItemResource;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Central entry point for all entities related to the EE RMA module.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RmaAccess
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var RmaInterface
     */
    private $currentRma;

    /**
     * @var ShipmentInterface
     */
    private $currentRmaShipment;

    /**
     * RmaAccess constructor.
     *
     * @param ModuleConfigInterface $config
     * @param Registry $registry
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ModuleConfigInterface $config,
        Registry $registry,
        ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->objectManager = $objectManager;
    }

    /**
     * @param RmaInterface $rma
     * @return void
     */
    public function setCurrentRma(RmaInterface $rma)
    {
        $this->currentRma = $rma;
    }

    /**
     * Register the return shipment as fetched from the platform api.
     *
     * @param ShipmentInterface $rmaShipment
     * @return void
     */
    public function setCurrentRmaShipment(ShipmentInterface $rmaShipment)
    {
        $this->currentRmaShipment = $rmaShipment;
    }

    /**
     * @return RmaInterface|null
     */
    public function getCurrentRma()
    {
        if ($this->currentRma instanceof RmaInterface) {
            return $this->currentRma;
        }

        // fall back to RMA registered in core registry
        return $this->registry->registry('current_rma');
    }

    /**
     * @return ShipmentInterface|null
     */
    public function getCurrentRmaShipment()
    {
        return $this->currentRmaShipment;
    }

    /**
     * Wrapper around RMA factory which is not available in CE and thus cannot be injected.
     *
     * @see \Magento\Rma\Api\Data\RmaInterfaceFactory::create
     *
     * @param mixed[] $data
     * @return RmaInterface
     */
    public function create($data = [])
    {
        $rma = $this->objectManager->create(RmaInterface::class, $data);
        return $rma;
    }

    /**
     * Wrapper around RMA repository which is not available in CE and thus cannot be injected.
     *
     * @see \Magento\Rma\Api\RmaRepositoryInterface::get
     *
     * @param int $rmaId
     * @return RmaInterface|null
     */
    public function getById($rmaId)
    {
        if (!$this->config->isRmaAvailable()) {
            return null;
        }

        /** @var RmaRepositoryInterface $rmaRepository */
        $rmaRepository = $this->objectManager->create(RmaRepositoryInterface::class);
        return $rmaRepository->get($rmaId);
    }

    /**
     * Wrapper around RMA item resource which is not available in CE and thus cannot be injected.
     *
     * @see \Magento\Rma\Model\ResourceModel\Item::getAuthorizedItems
     *
     * @param int $rmaId
     * @return mixed[]
     */
    public function getAuthorizedItems($rmaId)
    {
        if (!$this->config->isRmaAvailable()) {
            return [];
        }

        /** @var RmaItemResource $rmaItemResource */
        $rmaItemResource = $this->objectManager->create(RmaItemResource::class);
        return $rmaItemResource->getAuthorizedItems($rmaId);
    }
}
