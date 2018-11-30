<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\ViewModel\Config;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use Temando\Shipping\Model\Location;
use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * Temando Checkout Fields View Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CheckoutFieldsViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function backButtonIsAvailableInComponent()
    {
        /** @var CheckoutFields $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(CheckoutFields::class);
        $this->assertInstanceOf(PageActionsInterface::class, $viewModel);

        $actions = $viewModel->getMainActions();

        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertArrayHasKey('back', $actions);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     */
    public function shippingApiCredentialsAreAvailableInLocationComponents()
    {
        /** @var CheckoutFields $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(CheckoutFields::class);
        $this->assertInstanceOf(ShippingApiInterface::class, $viewModel);
        $this->assertEquals('https://foo.temando.io/v1/', $viewModel->getShippingApiAccess()->getApiEndpoint());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getUpdateCheckoutFieldEndpoint()
    {
        /** @var CheckoutFields $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(CheckoutFields::class);
        $this->assertContains('temando/settings_checkout/save', $viewModel->getUpdateCheckoutFieldEndpoint());
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     */
    public function getConfigUrl()
    {
        /** @var CheckoutFields $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(CheckoutFields::class);

        $url = $viewModel->getConfigurationPageUrl();
        $this->assertContains('system_config/edit', $url);
        $this->assertContains('carriers', $url);
        $this->assertContains('#carriers_temando-link', $url);
    }

    /**
     * @test
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/carriers/temando/additional_checkout_fields [{"foo": "bar"},{"fox": "baz"}]
     */
    public function getCheckoutFieldsData()
    {
        /** @var CheckoutFields $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(CheckoutFields::class);

        $fieldsData = $viewModel->getCheckoutFieldsData();
        $this->assertJson($fieldsData);
        $fields = json_decode($fieldsData);
        $this->assertInternalType('array', $fields);
        $this->assertCount(2, $fields);
    }
}
