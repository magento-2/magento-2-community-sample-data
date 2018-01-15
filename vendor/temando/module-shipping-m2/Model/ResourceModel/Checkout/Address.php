<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Checkout;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeInterfaceFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Model\Checkout\Address as AddressModel;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Checkout shipping address extension resource model
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Address extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Json
     */
    private $encoder;

    /**
     * @var AttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * Shipment constructor.
     * @param Context $context
     * @param EntityManager $entityManager
     * @param Json $encoder
     * @param AttributeInterfaceFactory $attributeFactory
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        Json $encoder,
        AttributeInterfaceFactory $attributeFactory,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->attributeFactory = $attributeFactory;

        parent::__construct($context, $connectionName);
    }

    /**
     * Init main table and primary key
     * @return void
     */
    protected function _construct()
    {
         $this->_init(SetupSchema::TABLE_CHECKOUT_ADDRESS, AddressInterface::ENTITY_ID);
    }

    /**
     * @param string[] $services
     * @return AttributeInterface[]
     */
    private function convertToAttributes(array $services)
    {
        $services = array_map(function ($value, $key) {
            /** @var AttributeInterface $attribute */
            $attribute = $this->attributeFactory->create();
            $attribute->setAttributeCode($key);
            $attribute->setValue($value);

            return $attribute;
        }, $services, array_keys($services));

        return $services;
    }

    /**
     * @param AttributeInterface[] $services
     * @return string[]
     */
    private function convertToArray(array $services)
    {
        $converted = [];
        foreach ($services as $service) {
            $converted[$service->getAttributeCode()] = $service->getValue();
        }

        return $converted;
    }

    /**
     * @param AbstractModel $object
     * @param int $value
     * @param null $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);

        /** @var AddressModel $object */
        $services = $object->getServiceSelection();
        if ($services && !is_array($services)) {
            $services = $this->encoder->unserialize($services);
            $services = $this->convertToAttributes($services);
            $object->setServiceSelection($services);
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        /** @var AddressModel $object */
        $services = $object->getServiceSelection();
        if (is_array($services)) {
            $services = $this->convertToArray($services);
            $object->setData(AddressInterface::SERVICE_SELECTION, $this->encoder->serialize($services));
        }

        $this->entityManager->save($object);

        return $this;
    }

    /**
     * @param int $quoteAddressId
     * @return string
     */
    public function getIdByQuoteAddressId($quoteAddressId)
    {
        $connection = $this->getConnection();
        $tableName  = $this->getMainTable();
        $table      = $this->getTable($tableName);

        $select = $connection->select()
            ->from($table, AddressInterface::ENTITY_ID)
            ->where('shipping_address_id = :shipping_address_id');

        $bind  = [':shipping_address_id' => (string)$quoteAddressId];

        return $connection->fetchOne($select, $bind);
    }
}
