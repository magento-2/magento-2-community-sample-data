<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Temando\Shipping\Api\Data\Shipment\ShipmentReferenceInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaShipment;

/**
 * Schema setup for use during installation / upgrade
 *
 * @package  Temando\Shipping\Setup
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RmaSetupSchema
{
    const TABLE_RMA_SHIPMENT = 'temando_rma_shipment';

    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * RmaSetupSchema constructor.
     * @param ModuleConfigInterface $moduleConfig
     */
    public function __construct(ModuleConfigInterface $moduleConfig)
    {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function createRmaShipmentTable(SchemaSetupInterface $installer)
    {
        if (!$this->moduleConfig->isRmaAvailable()) {
            return;
        }

        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::TABLE_RMA_SHIPMENT)
        );

        $table->addColumn(
            RmaShipment::RMA_ID,
            Table::TYPE_INTEGER,
            null,
            ['primary' => true, 'identity' => false, 'nullable' => false, 'unsigned' => true],
            'RMA ID'
        );

        $table->addColumn(
            RmaShipment::RMA_SHIPMENT_ID,
            Table::TYPE_TEXT,
            64,
            ['primary' => true, 'identity' => false, 'nullable' => false],
            'External Return Shipment ID'
        );

        $table->addForeignKey(
            $installer->getFkName(
                self::TABLE_RMA_SHIPMENT,
                RmaShipment::RMA_ID,
                'magento_rma',
                'entity_id'
            ),
            RmaShipment::RMA_ID,
            $installer->getTable('magento_rma'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table->setComment(
            'RMA to Return Shipment Associations'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $installer
     * @return void
     */
    public function addReturnShipmentIdColumn(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(SetupSchema::TABLE_SHIPMENT, SetupSchema::SALES_CONNECTION_NAME);
        $installer->getConnection(SetupSchema::SALES_CONNECTION_NAME)->addColumn(
            $tableName,
            ShipmentReferenceInterface::EXT_RETURN_SHIPMENT_ID,
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 64,
                'nullable' => true,
                'comment'  => 'External Return Shipment Id'
            ]
        );
    }
}
