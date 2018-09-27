<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\CustomerData;

use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Model\Checkout\Schema\CheckoutField;

/**
 * Temando Customer Data Checkout Fields Test
 *
 * @codingStandardsIgnoreFile
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CheckoutFieldsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     * @magentoConfigFixture default/carriers/temando/active 0
     * @magentoConfigFixture default/carriers/temando/additional_checkout_fields [{"id":"signature","label":"Signature","fieldType":"checkbox","orderPath":"#/address/type"}]
     */
    public function sectionDataIsEmptyWhenShippingIsDisabledInCheckout()
    {
        /** @var CheckoutFields $customerData */
        $customerData = Bootstrap::getObjectManager()->create(CheckoutFields::class);

        $sectionData = $customerData->getSectionData();
        $this->assertArrayHasKey('fields', $sectionData);
        $this->assertInternalType('array', $sectionData['fields']);
        $this->assertEmpty($sectionData['fields']);
    }

    /**
     * @test
     * @magentoConfigFixture default_store carriers/temando/active 1
     * @magentoConfigFixture default/carriers/temando/additional_checkout_fields [{"id":"signature","label":"Signature","fieldType":"checkbox","orderPath":"#/address/type"}]
     */
    public function sectionDataIsNotEmptyWhenShippingIsEnabledInCheckout()
    {
        /** @var CheckoutFields $customerData */
        $customerData = Bootstrap::getObjectManager()->create(CheckoutFields::class);

        $sectionData = $customerData->getSectionData();
        $this->assertArrayHasKey('fields', $sectionData);
        $this->assertInternalType('array', $sectionData['fields']);
        $this->assertNotEmpty($sectionData['fields']);
    }

    /**
     * @test
     * @magentoConfigFixture default_store carriers/temando/active 1
     * @magentoConfigFixture default/carriers/temando/additional_checkout_fields [{"id":"signature","label":"Signature","fieldType":"checkbox","orderPath":"#/address/type","defaultValue": true}]
     */
    public function checkboxTypeIsPrepared()
    {
        /** @var CheckoutFields $customerData */
        $customerData = Bootstrap::getObjectManager()->create(CheckoutFields::class);

        $sectionData = $customerData->getSectionData();
        $fields = $sectionData['fields'];
        $this->assertInternalType('array', $fields);
        $this->assertCount(1, $fields);

        /** @var CheckoutField $checkboxField */
        $checkboxField = $fields['signature'];
        $this->assertEquals('signature', $checkboxField['id']);
        $this->assertEquals('Signature', $checkboxField['label']);
        $this->assertEquals('checkbox', $checkboxField['type']);
        $this->assertEquals('#/address/type', $checkboxField['orderPath']);
        $this->assertTrue($checkboxField['defaultValue']);
        $this->assertEmpty($checkboxField['options']);
    }

    /**
     * @test
     * @magentoConfigFixture default_store carriers/temando/active 1
     * @magentoConfigFixture default/carriers/temando/additional_checkout_fields [{"id": "text","label": "Text","orderPath": "#/address/text","fieldType": "inputText","defaultValue": "Default"}]
     */
    public function textTypeIsPrepared()
    {
        /** @var CheckoutFields $customerData */
        $customerData = Bootstrap::getObjectManager()->create(CheckoutFields::class);

        $sectionData = $customerData->getSectionData();
        $fields = $sectionData['fields'];
        $this->assertInternalType('array', $fields);
        $this->assertCount(1, $fields);

        /** @var CheckoutField $textField */
        $textField = $fields['text'];
        $this->assertEquals('text', $textField['id']);
        $this->assertEquals('Text', $textField['label']);
        $this->assertEquals('text', $textField['type']);
        $this->assertEquals('#/address/text', $textField['orderPath']);
        $this->assertEquals('Default', $textField['defaultValue']);
        $this->assertEmpty($textField['options']);
    }

    /**
     * @test
     * @magentoConfigFixture default_store carriers/temando/active 1
     * @magentoConfigFixture default/carriers/temando/additional_checkout_fields [{"id": "select","label": "Select","orderPath": "#/address/select","fieldType": "select","defaultValue": "Two","options": [{"name": "One","value": "one"},{"name": "Two","value": "two"}]}]
     */
    public function selectTypeIsPrepared()
    {
        /** @var CheckoutFields $customerData */
        $customerData = Bootstrap::getObjectManager()->create(CheckoutFields::class);

        $sectionData = $customerData->getSectionData();
        $fields = $sectionData['fields'];
        $this->assertInternalType('array', $fields);
        $this->assertCount(1, $fields);

        /** @var CheckoutField $selectField */
        $selectField = $fields['select'];
        $this->assertEquals('select', $selectField['id']);
        $this->assertEquals('Select', $selectField['label']);
        $this->assertEquals('select', $selectField['type']);
        $this->assertEquals('#/address/select', $selectField['orderPath']);
        $this->assertEquals('Two', $selectField['defaultValue']);
        $this->assertNotEmpty($selectField['options']);
        $this->assertInternalType('array', $selectField['options']);
    }
}
