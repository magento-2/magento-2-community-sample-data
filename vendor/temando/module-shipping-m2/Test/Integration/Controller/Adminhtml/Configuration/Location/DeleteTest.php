<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Location;

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
    protected $resource = 'Temando_Shipping::locations';

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = 'backend/temando/configuration_location/delete';

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function redirectIfNoLocationIdParameterGiven()
    {
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_location/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $errorMessage = $messageManager->getMessages(true)->getLastAddedMessage();
        $this->assertEquals(MessageInterface::TYPE_ERROR, $errorMessage->getType());
        $this->assertContains('Location ID missing', $errorMessage->getText());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function locationDeleteFailure()
    {
        $locationId = '1234-abcd';

        $errorMessage = __('Foo');
        $apiAdapterMock = $this->getMockBuilder(\Temando\Shipping\Rest\Adapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $apiAdapterMock
            ->expects($this->once())
            ->method('deleteLocation')
            ->willThrowException(new CouldNotDeleteException($errorMessage));
        Bootstrap::getObjectManager()->addSharedInstance($apiAdapterMock, \Temando\Shipping\Rest\Adapter::class);

        $this->getRequest()->setParam('location_id', $locationId);
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_location/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $errorMessage = $messageManager->getMessages(true)->getLastAddedMessage();
        $this->assertEquals(MessageInterface::TYPE_ERROR, $errorMessage->getType());
        $this->assertContains('An error occurred while deleting the location', $errorMessage->getText());
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function locationDeleteOk()
    {
        $locationId = '1234-abcd';

        $apiAdapterMock = $this->getMockBuilder(\Temando\Shipping\Rest\Adapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $apiAdapterMock
            ->expects($this->once())
            ->method('deleteLocation');
        Bootstrap::getObjectManager()->addSharedInstance($apiAdapterMock, \Temando\Shipping\Rest\Adapter::class);

        $this->getRequest()->setParam('location_id', $locationId);
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_location/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $successMessage = $messageManager->getMessages(true)->getLastAddedMessage();
        $this->assertEquals(MessageInterface::TYPE_SUCCESS, $successMessage->getType());
        $this->assertContains('Location was deleted successfully', $successMessage->getText());
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
