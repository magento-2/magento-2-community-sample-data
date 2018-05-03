<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\ViewModel\Packaging;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use Temando\Shipping\Model\Packaging;
use Temando\Shipping\Model\PackagingInterface;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * Temando Packaging View Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class PackagingViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return PackagingInterface
     */
    private function getPackaging()
    {
        $packaging = Bootstrap::getObjectManager()->create(Packaging::class, ['data' => [
            PackagingInterface::NAME => 'Foo Packaging',
            PackagingInterface::PACKAGING_ID => '00000000-6000-0006-0000-000000000000',
        ]]);

        return $packaging;
    }

    /**
     * @test
     */
    public function backButtonIsAvailableInEditComponents()
    {
        /** @var PackagingEdit $packagingEdit */
        $packagingEdit = Bootstrap::getObjectManager()->get(PackagingEdit::class);
        $this->assertInstanceOf(PageActionsInterface::class, $packagingEdit);

        $actions = $packagingEdit->getMainActions();

        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertArrayHasKey('back', $actions);
    }

    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/session_endpoint https://auth.temando.io/v1/
     * @magentoConfigFixture default/carriers/temando/sovereign_endpoint https://foo.temando.io/v1/
     */
    public function shippingApiCredentialsAreAvailableInPackagingComponents()
    {
        /** @var PackagingEdit $packagingEdit */
        $packagingEdit = Bootstrap::getObjectManager()->get(PackagingEdit::class);
        $this->assertInstanceOf(ShippingApiInterface::class, $packagingEdit);
        $this->assertEquals('https://foo.temando.io/v1/', $packagingEdit->getShippingApiAccess()->getApiEndpoint());
    }

    /**
     * @test
     */
    public function packagingIdIsAvailableInEditComponent()
    {
        $packaging = $this->getPackaging();

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->exactly(1))
            ->method('getParam')
            ->with(PackagingInterface::PACKAGING_ID)
            ->willReturn($packaging->getPackagingId());

        /** @var PackagingEdit $packagingEdit */
        $packagingEdit = Bootstrap::getObjectManager()->create(PackagingEdit::class, [
            'request' => $request,
        ]);
        $this->assertEquals($packaging->getPackagingId(), $packagingEdit->getPackagingId());
    }

    /**
     * @test
     */
    public function entityUrlsAreAvailableInPackagingComponents()
    {
        /** @var Packaging $packaging */
        $packaging = $this->getPackaging();

        /** @var PackagingEdit $packagingEdit */
        $packagingEdit = Bootstrap::getObjectManager()->get(PackagingEdit::class);
        $this->assertInstanceOf(EntityUrlInterface::class, $packagingEdit->getPackagingUrl());

        // application does not provide view action for containers
        $this->assertEmpty($packagingEdit->getPackagingUrl()->getViewActionUrl($packaging->getData()));
        $this->assertContains('new', $packagingEdit->getPackagingUrl()->getNewActionUrl());
        $this->assertContains('index', $packagingEdit->getPackagingUrl()->getListActionUrl());

        $editUrl = $packagingEdit->getPackagingUrl()->getEditActionUrl($packaging->getData());
        $this->assertContains('edit', $editUrl);
        $this->assertContains($packaging->getPackagingId(), $editUrl);

        $deleteUrl = $packagingEdit->getPackagingUrl()->getDeleteActionUrl($packaging->getData());
        $this->assertContains('delete', $deleteUrl);
        $this->assertContains($packaging->getPackagingId(), $deleteUrl);
    }

    /**
     * @test
     */
    public function maliciousParamValuesGetStripped()
    {
        $badPackagingId = '<script>alert("packaging");</script>';

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->exactly(1))
            ->method('getParam')
            ->with(PackagingInterface::PACKAGING_ID)
            ->willReturn($badPackagingId);

        /** @var PackagingEdit $packagingEdit */
        $packagingEdit = Bootstrap::getObjectManager()->create(PackagingEdit::class, [
            'request' => $request,
        ]);
        $this->assertRegExp('/^[\w0-9-_]+$/', $packagingEdit->getPackagingId());
    }
}
