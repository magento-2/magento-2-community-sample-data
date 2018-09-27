<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Dispatch;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Temando\Shipping\Model\Dispatch;
use Temando\Shipping\Model\DispatchProvider;
use Temando\Shipping\Model\ResourceModel\Dispatch\DispatchRepository;

/**
 * @magentoAppArea adminhtml
 */
class ViewTest extends AbstractBackendController
{
    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = 'Temando_Shipping::dispatches';

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = 'backend/temando/dispatch/view';

    /**
     * @var DispatchRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatchRepo;

    protected function setUp()
    {
        parent::setUp();

        $this->dispatchRepo = $this->getMockBuilder(DispatchRepository::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();

        Bootstrap::getObjectManager()->addSharedInstance($this->dispatchRepo, DispatchRepository::class);
    }

    protected function tearDown()
    {
        Bootstrap::getObjectManager()->removeSharedInstance(DispatchProvider::class);

        parent::tearDown();
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function dispatchLoadSuccess()
    {
        $dispatchId = '1234-abcd';
        $carrierName = 'Foo';
        $createdAtDate = '1999-01-19T03:03:33.000Z';
        $readyAtDate = '2099-01-19T03:03:33.000Z';
        $shipmentCount = '42';

        $dispatch = new Dispatch([
            Dispatch::DISPATCH_ID => $dispatchId,
            Dispatch::CARRIER_NAME => $carrierName,
            Dispatch::CREATED_AT_DATE => $createdAtDate,
            Dispatch::READY_AT_DATE => $readyAtDate,
            Dispatch::INCLUDED_SHIPMENTS => $shipmentCount,
        ]);

        $this->dispatchRepo
            ->expects($this->once())
            ->method('getById')
            ->with($dispatchId)
            ->willReturn($dispatch);

        $this->getRequest()->setParam('dispatch_id', $dispatchId);
        $this->dispatch($this->uri);

        $this->assertContains($carrierName, $this->getResponse()->getBody());
        $this->assertContains('Documentation', $this->getResponse()->getBody());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function dispatchLoadError()
    {
        $dispatchId = '1234-abcd';

        $this->dispatchRepo
            ->expects($this->once())
            ->method('getById')
            ->with($dispatchId)
            ->willThrowException(new NoSuchEntityException(__('Not found.')));

        $this->getRequest()->setParam('dispatch_id', $dispatchId);
        $this->dispatch($this->uri);

        $this->assertNotContains('Documentation', $this->getResponse()->getBody());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclHasAccess()
    {
        $dispatchId = '1234-abcd';

        $dispatch = new Dispatch([
            Dispatch::DISPATCH_ID => $dispatchId,
        ]);

        $this->dispatchRepo
            ->expects($this->once())
            ->method('getById')
            ->with($dispatchId)
            ->willReturn($dispatch);

        $this->getRequest()->setParam('dispatch_id', $dispatchId);

        parent::testAclHasAccess();
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclNoAccess()
    {
        $dispatchId = '1234-abcd';

        $dispatch = new Dispatch([
            Dispatch::DISPATCH_ID => $dispatchId,
        ]);

        $this->dispatchRepo
            ->expects($this->once())
            ->method('getById')
            ->with($dispatchId)
            ->willReturn($dispatch);

        $this->getRequest()->setParam('dispatch_id', $dispatchId);

        parent::testAclNoAccess();
    }
}
