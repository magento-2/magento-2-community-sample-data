<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Temando\Shipping\Model\Order;
use Temando\Shipping\Model\OrderInterface;

class OrderTest extends \PHPUnit\Framework\TestCase
{
    /** @var ObjectManager $objectManager */
    private $objectManager;
    /** @var Order $order*/
    private $order;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->order = $this->objectManager->create(Order::class);
        $this->order->setData(OrderInterface::AMOUNT, 'AMOUNT');
        $this->order->setData(OrderInterface::CREATED_AT, 'CREATED_AT');
        $this->order->setData(OrderInterface::CURRENCY, 'CURRENCY');
        $this->order->setData(OrderInterface::LAST_MODIFIED_AT, 'LAST_MODIFIED_AT');
        $this->order->setData(OrderInterface::ORDER_ID, 'ORDER_ID');
        $this->order->setData(OrderInterface::ORDER_ITEMS, 'ORDER_ITEMS');
        $this->order->setData(OrderInterface::ORDERED_AT, 'ORDERED_AT');
        $this->order->setData(OrderInterface::RECIPIENT, 'RECIPIENT');
        $this->order->setData(OrderInterface::SELECTED_EXPERIENCE_AMOUNT, 'SELECTED_EXPERIENCE_AMOUNT');
        $this->order->setData(OrderInterface::SELECTED_EXPERIENCE_CODE, 'SELECTED_EXPERIENCE_CODE');
        $this->order->setData(OrderInterface::SELECTED_EXPERIENCE_CURRENCY, 'SELECTED_EXPERIENCE_CURRENCY');
        $this->order->setData(OrderInterface::SELECTED_EXPERIENCE_DESCRIPTION, 'SELECTED_EXPERIENCE_DESCRIPTION');
        $this->order->setData(OrderInterface::SELECTED_EXPERIENCE_LANGUAGE, 'SELECTED_EXPERIENCE_LANGUAGE');
        $this->order->setData(OrderInterface::BILLING, 'BILLING');
        $this->order->setData(OrderInterface::SOURCE_ID, 'SOURCE_ID');
        $this->order->setData(OrderInterface::SOURCE_INCREMENT_ID, 'SOURCE_INCREMENT_ID');
        $this->order->setData(OrderInterface::SOURCE_REFERENCE, 'SOURCE_REFERENCE');
    }

    /**
     * @test
     */
    public function getAmountTest()
    {
        $result = $this->order->getAmount();
        $this->assertEquals($result, "AMOUNT");
    }
    /**
     * @test
     */
    public function getCreatedAtTest()
    {
        $result = $this->order->getCreatedAt();
        $this->assertEquals($result, "CREATED_AT");
    }
    /**
     * @test
     */
    public function getCurrencyTest()
    {
        $result = $this->order->getCurrency();
        $this->assertEquals($result, "CURRENCY");
    }
    /**
     * @test
     */
    public function getLastModifiedAtTest()
    {
        $result = $this->order->getLastModifiedAt();
        $this->assertEquals($result, "LAST_MODIFIED_AT");
    }
    /**
     * @test
     */
    public function getOrderIdTest()
    {
        $result = $this->order->getOrderId();
        $this->assertEquals($result, "ORDER_ID");
    }
    /**
     * @test
     */
    public function getOrderItemsTest()
    {
        $result = $this->order->getOrderItems();
        $this->assertEquals($result, "ORDER_ITEMS");
    }
    /**
     * @test
     */
    public function getOrderedAtTest()
    {
        $result = $this->order->getOrderedAt();
        $this->assertEquals($result, "ORDERED_AT");
    }
    /**
     * @test
     */
    public function getrecipientTest()
    {
        $result = $this->order->getRecipient();
        $this->assertEquals($result, "RECIPIENT");
    }
    /**
     * @test
     */
    public function getSelectedExperienceAmountTest()
    {
        $result = $this->order->getExperienceAmount();
        $this->assertEquals($result, "SELECTED_EXPERIENCE_AMOUNT");
    }
    /**
     * @test
     */
    public function getExperienceCodeTest()
    {
        $result = $this->order->getExperienceCode();
        $this->assertEquals($result, "SELECTED_EXPERIENCE_CODE");
    }
    /**
     * @test
     */
    public function getExperienceCurrencyTest()
    {
        $result = $this->order->getExperienceCurrency();
        $this->assertEquals($result, "SELECTED_EXPERIENCE_CURRENCY");
    }
    /**
     * @test
     */
    public function getExperienceDescriptionTest()
    {
        $result = $this->order->getExperienceDescription();
        $this->assertEquals($result, "SELECTED_EXPERIENCE_DESCRIPTION");
    }
    /**
     * @test
     */
    public function getExperienceLanguageTest()
    {
        $result = $this->order->getExperienceLanguage();
        $this->assertEquals($result, "SELECTED_EXPERIENCE_LANGUAGE");
    }
    /**
     * @test
     */
    public function getBillingTest()
    {
        $result = $this->order->getBilling();
        $this->assertEquals($result, "BILLING");
    }
    /**
     * @test
     */
    public function getSourceIdTest()
    {
        $result = $this->order->getSourceId();
        $this->assertEquals($result, "SOURCE_ID");
    }
    /**
     * @test
     */
    public function getSourceIncrementIdTest()
    {
        $result = $this->order->getSourceIncrementId();
        $this->assertEquals($result, "SOURCE_INCREMENT_ID");
    }
    /**
     * @test
     */
    public function getSourceReferenceTest()
    {
        $result = $this->order->getSourceReference();
        $this->assertEquals($result, "SOURCE_REFERENCE");
    }
}
