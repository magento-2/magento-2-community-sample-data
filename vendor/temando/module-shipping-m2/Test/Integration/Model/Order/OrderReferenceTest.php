<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model;

use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;

/**
 * Temando Order Reference Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderReferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $entityId = 303;
        $orderId = 808;
        $extOrderId = 'F00-O01';
        $shippingExperiences = ['foo' => 'bar'];

        /** @var OrderReferenceInterface $orderReference */
        $orderReference = $this->objectManager->create(OrderReferenceInterface::class, ['data' => [
            OrderReferenceInterface::ENTITY_ID => $entityId,
            OrderReferenceInterface::ORDER_ID => $orderId,
            OrderReferenceInterface::EXT_ORDER_ID => $extOrderId,
            OrderReferenceInterface::SHIPPING_EXPERIENCES => $shippingExperiences,
        ]]);

        $this->assertEquals($entityId, $orderReference->getEntityId());
        $this->assertEquals($orderId, $orderReference->getOrderId());
        $this->assertEquals($extOrderId, $orderReference->getExtOrderId());
        $this->assertEquals($shippingExperiences, $orderReference->getShippingExperiences());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $entityId = 303;
        $orderId = 808;
        $extOrderId = 'F00-O01';
        $shippingExperiences = ['foo' => 'bar'];

        /** @var OrderReferenceInterface $orderReference */
        $orderReference = $this->objectManager->create(OrderReferenceInterface::class);
        $this->assertEmpty($orderReference->getEntityId());

        $orderReference->setEntityId($entityId);
        $this->assertEquals($entityId, $orderReference->getEntityId());

        $orderReference->setOrderId($orderId);
        $this->assertEquals($orderId, $orderReference->getOrderId());

        $orderReference->setExtOrderId($extOrderId);
        $this->assertEquals($extOrderId, $orderReference->getExtOrderId());

        $orderReference->setShippingExperiences($shippingExperiences);
        $this->assertEquals($shippingExperiences, $orderReference->getShippingExperiences());
    }
}
