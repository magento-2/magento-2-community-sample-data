<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Tax\Api\TaxClassManagementInterface;

/**
 * Data Installation Script
 *
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Install Tax Classes & Customer Attribute
     *
     * {@inheritDoc}
     *
     * MEQP2 Warning: $context necessary for interface
     *
     * @see \Magento\Framework\Setup\InstallDataInterface::install()
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $taxClassTable = $setup->getTable('tax_class');
        $select = $setup->getConnection()->select()->from($taxClassTable);
        $query = $setup->getConnection()->query($select);
        $results = $query->fetchAll();
        $classNames = array_map(
            function ($result) {
                return $result['class_name'];
            },
            $results
        );

        $data = [
            [
                'class_name' => 'Refund Adjustments',
                'class_type' => TaxClassManagementInterface::TYPE_PRODUCT
            ],
            [
                'class_name' => 'Gift Options',
                'class_type' => TaxClassManagementInterface::TYPE_PRODUCT
            ],
            [
                'class_name' => 'Order Gift Wrapping',
                'class_type' => TaxClassManagementInterface::TYPE_PRODUCT
            ],
            [
                'class_name' => 'Item Gift Wrapping',
                'class_type' => TaxClassManagementInterface::TYPE_PRODUCT
            ],
            [
                'class_name' => 'Printed Gift Card',
                'class_type' => TaxClassManagementInterface::TYPE_PRODUCT
            ],
            [
                'class_name' => 'Reward Points',
                'class_type' => TaxClassManagementInterface::TYPE_PRODUCT
            ]
        ];

        foreach ($data as $row) {
            if (!in_array($row['class_name'], $classNames, true)) {
                $setup->getConnection()->insertForce($taxClassTable, $row);
            }
        }
    }
}
