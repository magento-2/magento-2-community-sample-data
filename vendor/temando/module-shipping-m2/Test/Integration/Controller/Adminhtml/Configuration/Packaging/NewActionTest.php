<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Configuration\Packaging;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Temando\Shipping\Rest\AuthenticationInterface;

/**
 * @magentoAppArea adminhtml
 */
class NewActionTest extends AbstractBackendController
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
    protected $uri = 'backend/temando/configuration_packaging/new';

    protected function setUp()
    {
        parent::setUp();

        /** @var SessionManagerInterface $adminSession */
        $adminSession = Bootstrap::getObjectManager()->get(SessionManagerInterface::class);
        $adminSession->setData(AuthenticationInterface::DATA_KEY_SESSION_TOKEN_EXPIRY, '2038-01-19T03:03:33.000Z');
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/account_id 23
     * @magentoConfigFixture default/carriers/temando/bearer_token 808
     */
    public function pageIsRendered()
    {
        $this->dispatch($this->uri);
        $this->assertContains('ContainerForm', $this->getResponse()->getBody());
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
