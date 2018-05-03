<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Schema Installation Script
 *
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Create table 'vertex_taxrequest'
     *
     * {@inheritDoc}
     *
     * MEQP2 Warning: $context necessary for interface
     *
     * @see \Magento\Framework\Setup\InstallSchemaInterface::install()
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength) Over 100 lines due to very verbose table creation
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()
            ->newTable($setup->getTable('vertex_taxrequest'))
            ->addColumn(
                'request_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Request Id'
            )
            ->addColumn(
                'request_type',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Request Type'
            )
            ->addColumn(
                'quote_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default' => '0'
                ],
                'Quote ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default' => '0'
                ],
                'Order ID'
            )
            ->addColumn(
                'total_tax',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Total Tax Amount'
            )
            ->addColumn(
                'source_path',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Source path controller_module_action'
            )
            ->addColumn(
                'tax_area_id',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Tax Jurisdictions Id'
            )
            ->addColumn(
                'sub_total',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Response Subtotal Amount'
            )
            ->addColumn(
                'total',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Response Total Amount'
            )
            ->addColumn(
                'lookup_result',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Tax Area Response Lookup Result'
            )
            ->addColumn(
                'request_date',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Request create date'
            )
            ->addColumn(
                'request_xml',
                Table::TYPE_TEXT,
                '64k',
                [
                    'nullable' => false
                ],
                'Request XML'
            )
            ->addColumn(
                'response_xml',
                Table::TYPE_TEXT,
                '64k',
                [
                    'nullable' => false
                ],
                'Response XML'
            )
            ->addIndex(
                $setup->getIdxName(
                    'vertex_taxrequest',
                    [
                        'request_id'
                    ],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [
                    'request_id'
                ],
                [
                    'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                ]
            )
            ->addIndex(
                $setup->getIdxName(
                    'vertex_taxrequest',
                    [
                        'request_type'
                    ]
                ),
                [
                    'request_type'
                ]
            )
            ->addIndex(
                $setup->getIdxName(
                    'vertex_taxrequest',
                    [
                        'order_id'
                    ]
                ),
                [
                    'order_id'
                ]
            )
            ->setComment('Log of requests to Vertex');
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
