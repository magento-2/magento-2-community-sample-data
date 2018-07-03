<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Order;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Test\Integration\Fixture\PlacedOrderFixture;

/**
 * Temando Order Repository Test
 *
 * @magentoAppIsolation enabled
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * delegate fixtures creation to separate class.
     */
    public static function createOrderReferenceFixture()
    {
        PlacedOrderFixture::createOrderReferenceFixture();
    }

    /**
     * Set valid session token
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, '2038-01-19T03:03:33.000Z');
    }

    protected function tearDown()
    {
        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->unsetData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY);

        parent::tearDown();
    }

    /**
     * @test
     * @magentoDataFixture createOrderReferenceFixture
     */
    public function loadOrderReferenceByExternalIdSuccess()
    {
        $extOrderId = PlacedOrderFixture::getExternalOrderId();

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepositoryInterface::class);
        $orderReference = $orderRepository->getReferenceByExtOrderId($extOrderId);

        $this->assertInstanceOf(OrderReferenceInterface::class, $orderReference);
        $this->assertEquals($extOrderId, $orderReference->getExtOrderId());
    }

    /**
     * @test
     */
    public function loadOrderReferenceByExternalIdFailure()
    {
        $extOrderId = 'xxx';

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage(
            sprintf('Order reference to "%1$s" does not exist.', $extOrderId)
        );

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepositoryInterface::class);
        $orderRepository->getReferenceByExtOrderId($extOrderId);
    }

    /**
     * @test
     * @magentoDataFixture createOrderReferenceFixture
     */
    public function loadOrderReferenceByOrderIdSuccess()
    {
        $orderIncrementId = PlacedOrderFixture::getOrderIncrementId();

        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $orderIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $orderRepository = Bootstrap::getObjectManager()->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $searchResult = $orderRepository->getList($searchCriteriaBuilder->create());
        /** @var \Magento\Sales\Model\Order $order */
        $order = $searchResult->getFirstItem();

        $orderId = $order->getId();

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepositoryInterface::class);
        $orderReference = $orderRepository->getReferenceByOrderId($orderId);

        $this->assertInstanceOf(OrderReferenceInterface::class, $orderReference);
        $this->assertEquals($orderId, $orderReference->getOrderId());
    }

    /**
     * @test
     */
    public function loadOrderReferenceByOrderIdFailure()
    {
        $orderId = 303;

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage(
            sprintf('Order reference for order "%1$s" does not exist.', $orderId)
        );

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->get(OrderRepositoryInterface::class);
        $orderRepository->getReferenceByOrderId($orderId);
    }

    /**
     * @test
     */
    public function saveReferenceSuccess()
    {
        $orderId = 303;
        $extOrderId = 'xxx';

        $orderReference = Bootstrap::getObjectManager()->create(OrderReferenceInterface::class, ['data' => [
            OrderReferenceInterface::ORDER_ID => $orderId,
            OrderReferenceInterface::EXT_ORDER_ID => $extOrderId,
        ]]);

        $resourceModelMock = $this->getMockBuilder(OrderReference::class)
            ->setMethods(['load', 'save', 'delete'])
            ->disableOriginalConstructor()
            ->getMock();
        $resourceModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->create(OrderRepositoryInterface::class, [
            'resource' => $resourceModelMock,
        ]);

        $orderRepository->saveReference($orderReference);
    }

    /**
     * @test
     */
    public function saveReferenceFailure()
    {
        $orderId = 303;
        $extOrderId = 'xxx';
        $message = 'Unable to save order reference.';

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage($message);

        $orderReference = Bootstrap::getObjectManager()->create(OrderReferenceInterface::class, ['data' => [
            OrderReferenceInterface::ORDER_ID => $orderId,
            OrderReferenceInterface::EXT_ORDER_ID => $extOrderId,
        ]]);

        $resourceModelMock = $this->getMockBuilder(OrderReference::class)
            ->setMethods(['load', 'save', 'delete'])
            ->disableOriginalConstructor()
            ->getMock();
        $resourceModelMock->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception($message));
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->create(OrderRepositoryInterface::class, [
            'resource' => $resourceModelMock,
        ]);

        $orderRepository->saveReference($orderReference);
    }
}
