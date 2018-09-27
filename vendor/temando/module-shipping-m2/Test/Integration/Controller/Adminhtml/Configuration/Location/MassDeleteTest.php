<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Location;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\Message\MessageInterface;
use Zend\Http\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends AbstractBackendController
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
    protected $uri = 'backend/temando/configuration_location/massdelete';

    /**
     * All locations fail to be deleted. Assert messages being collected.
     *
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function locationDeleteFailure()
    {
        $locationIds = [
            '1234-abcd',
            '5678-efgh',
        ];
        $errorText = 'Foo';

        $apiAdapterMock = $this->getMockBuilder(\Temando\Shipping\Rest\Adapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $apiAdapterMock
            ->expects($this->exactly(2))
            ->method('deleteLocation')
            ->willThrowException(new CouldNotDeleteException(__($errorText)));
        Bootstrap::getObjectManager()->addSharedInstance($apiAdapterMock, \Temando\Shipping\Rest\Adapter::class);

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue([
            'form_key' => Bootstrap::getObjectManager()->get(FormKey::class)->getFormKey(),
            'selected' => $locationIds,
        ]);
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_location/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $errors = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_ERROR);
        $warnings = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_WARNING);
        $success = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_SUCCESS);
        $this->assertCount(3, $errors); // 2 items + general error
        $this->assertCount(1, $warnings); // action result message
        $this->assertCount(0, $success);
    }

    /**
     * Some locations fail to be deleted. Assert messages being collected.
     *
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function locationDeletePartialFailure()
    {
        $locationIds = [
            '1234-abcd',
            '5678-efgh',
        ];
        $errorText = 'Foo';

        $apiAdapterMock = $this->getMockBuilder(\Temando\Shipping\Rest\Adapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $apiAdapterMock
            ->expects($this->exactly(2))
            ->method('deleteLocation')
            ->will(
                $this->onConsecutiveCalls(
                    $this->throwException(new CouldNotDeleteException(__($errorText))),
                    $this->returnSelf()
                )
            );
        Bootstrap::getObjectManager()->addSharedInstance($apiAdapterMock, \Temando\Shipping\Rest\Adapter::class);

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue([
            'form_key' => Bootstrap::getObjectManager()->get(FormKey::class)->getFormKey(),
            'selected' => $locationIds,
        ]);
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_location/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $errors = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_ERROR);
        $warnings = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_WARNING);
        $success = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_SUCCESS);
        $this->assertCount(2, $errors); // 1 item + general error
        $this->assertCount(1, $warnings); // action result message
        $this->assertCount(0, $success);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function locationDeleteSuccess()
    {
        $locationIds = [
            '1234-abcd',
            '5678-efgh',
        ];

        $apiAdapterMock = $this->getMockBuilder(\Temando\Shipping\Rest\Adapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $apiAdapterMock
            ->expects($this->exactly(2))
            ->method('deleteLocation');
        Bootstrap::getObjectManager()->addSharedInstance($apiAdapterMock, \Temando\Shipping\Rest\Adapter::class);

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue([
            'form_key' => Bootstrap::getObjectManager()->get(FormKey::class)->getFormKey(),
            'selected' => $locationIds,
        ]);
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('backend/temando/configuration_location/index'));

        /** @var MessageManager $messageManager */
        $messageManager = Bootstrap::getObjectManager()->get(MessageManager::class);
        $errors = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_ERROR);
        $warnings = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_WARNING);
        $success = $messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_SUCCESS);
        $this->assertCount(0, $errors); // no errors occurred
        $this->assertCount(0, $warnings);
        $this->assertCount(1, $success); // action result message
    }

    /**
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclHasAccess()
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue([
            'form_key' => Bootstrap::getObjectManager()->get(FormKey::class)->getFormKey(),
            'selected' => [
                '1234-abcd',
                '5678-efgh',
            ],
        ]);

        parent::testAclHasAccess();
    }

    /**
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function testAclNoAccess()
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPostValue([
            'form_key' => Bootstrap::getObjectManager()->get(FormKey::class)->getFormKey(),
            'selected' => [],
        ]);

        parent::testAclNoAccess();
    }
}
