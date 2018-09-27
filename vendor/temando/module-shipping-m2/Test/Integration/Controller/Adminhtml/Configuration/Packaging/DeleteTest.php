<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Packaging;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class DeleteTest extends AbstractBackendController
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
    protected $uri = 'backend/temando/configuration_packaging/delete';

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function redirectIfNoPackagingIdParameterGiven()
    {
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_packaging/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $errorMessage = $messageManager->getMessages(true)->getLastAddedMessage();
        $this->assertEquals(MessageInterface::TYPE_ERROR, $errorMessage->getType());
        $this->assertContains('Container ID missing', $errorMessage->getText());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function packagingDeleteFailure()
    {
        $containerId = '1234-abcd';

        $errorMessage = __('Foo');
        $apiAdapterMock = $this->getMockBuilder(\Temando\Shipping\Rest\Adapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $apiAdapterMock
            ->expects($this->once())
            ->method('deleteContainer')
            ->willThrowException(new CouldNotDeleteException($errorMessage));
        Bootstrap::getObjectManager()->addSharedInstance($apiAdapterMock, \Temando\Shipping\Rest\Adapter::class);

        $this->getRequest()->setParam('packaging_id', $containerId);
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_packaging/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $errorMessage = $messageManager->getMessages(true)->getLastAddedMessage();
        $this->assertEquals(MessageInterface::TYPE_ERROR, $errorMessage->getType());
        $this->assertContains('An error occurred while deleting the container', $errorMessage->getText());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function packagingDeleteOk()
    {
        $containerId = '1234-abcd';

        $apiAdapterMock = $this->getMockBuilder(\Temando\Shipping\Rest\Adapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $apiAdapterMock
            ->expects($this->once())
            ->method('deleteContainer');
        Bootstrap::getObjectManager()->addSharedInstance($apiAdapterMock, \Temando\Shipping\Rest\Adapter::class);

        $this->getRequest()->setParam('packaging_id', $containerId);
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_packaging/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $successMessage = $messageManager->getMessages(true)->getLastAddedMessage();
        $this->assertEquals(MessageInterface::TYPE_SUCCESS, $successMessage->getType());
        $this->assertContains('Container was deleted successfully', $successMessage->getText());
    }

    /**
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
