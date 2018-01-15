<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Packaging;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Temando\Shipping\Model\ResourceModel\Packaging\PackagingRepository;

/**
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = 'Temando_Shipping::packaging';

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = 'backend/temando/configuration_packaging/index';

    protected function setUp()
    {
        parent::setUp();

        $repositoryMock = $this->getMockBuilder(PackagingRepository::class)
            ->setMethods(['getList'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock
            ->expects($this->any())
            ->method('getList')
            ->willReturn([]);
        Bootstrap::getObjectManager()->addSharedInstance($repositoryMock, PackagingRepository::class);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function pageIsRendered()
    {
        $this->dispatch($this->uri);
        $this->assertContains('Create New Packaging', $this->getResponse()->getBody());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
