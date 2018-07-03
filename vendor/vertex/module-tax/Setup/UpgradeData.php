<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Data Upgrade Script
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /** @var Config */
    private $eavConfig;

    /** @var AttributeRepositoryInterface */
    private $attributeRepository;

    /**
     * @param Config $eavConfig
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(Config $eavConfig, AttributeRepositoryInterface $attributeRepository)
    {
        $this->eavConfig = $eavConfig;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '100.1.0') < 0) {
            $this->migrateCustomAttributeToExtensionAttribute($setup);
            $this->deleteCustomAttribute();
        }
    }

    /**
     * Deletes the "customer_code" custom attribute, if created
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function deleteCustomAttribute()
    {
        $attribute = $this->getEntityAttribute(Customer::ENTITY, 'customer_code');
        if (!$attribute) {
            return;
        }
        $this->attributeRepository->delete($attribute);
    }

    /**
     * Perform migration of custom attributes to extension attributes
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function migrateCustomAttributeToExtensionAttribute(ModuleDataSetupInterface $setup)
    {
        $db = $setup->getConnection();
        $attribute = $this->getEntityAttribute(Customer::ENTITY, 'customer_code');
        if (!$attribute) {
            return;
        }

        $select = $db->select()
            ->from($setup->getTable('customer_entity_varchar'), ['entity_id', 'value'])
            ->where('attribute_id = ?', $attribute->getId());

        $results = array_map(
            function ($rawResult) {
                return [
                    'customer_id' => $rawResult['entity_id'],
                    'customer_code' => $rawResult['value'],
                ];
            },
            $db->fetchAll($select)
        );

        if (!count($results)) {
            return;
        }

        $db->insertMultiple(
            $setup->getTable('vertex_customer_code'),
            $results
        );
    }

    /**
     * Retrieve an entity attribute
     *
     * @param string $entity
     * @param string $attributeCode
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|void
     * @throws LocalizedException
     */
    private function getEntityAttribute($entity, $attributeCode)
    {
        if (method_exists($this->eavConfig, 'getEntityAttributes')) {
            $attributes = $this->eavConfig->getEntityAttributes($entity);
            if (!isset($attributes[$attributeCode])) {
                return;
            }

            return $attributes[$attributeCode];
        }

        $attributeCodes = $this->eavConfig->getEntityAttributeCodes($entity);
        if (!in_array($attributeCode, $attributeCodes)) {
            return;
        }

        return $this->eavConfig->getAttribute($entity, $attributeCode);
    }
}
