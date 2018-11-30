<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Shipment;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReference as ShipmentReferenceResource;
use Temando\Shipping\Model\Shipment\ShipmentReference;

/**
 * Temando Shipment Reference Resource Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentReferenceCollection extends AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'temando_shipment_reference_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'shipment_reference_collection';

    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ModuleConfigInterface $config
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ModuleConfigInterface $config,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->config = $config;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Init collection and determine table names
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ShipmentReference::class, ShipmentReferenceResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $salesShipmentTable = $this->getTable('sales_shipment');
        $salesOrderTable = $this->getTable('sales_order');

        $this->getSelect()
            ->join(['s' => $salesShipmentTable], 's.entity_id = main_table.shipment_id', [])
            ->join(['o' => $salesOrderTable], 'o.entity_id = s.order_id', []);

        return $this;
    }
}
