<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Directory\Model\Currency;
use Magento\Framework\Api\AbstractSimpleObjectBuilder;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Setup\SetupData;

/**
 * Temando Order Item Builder
 *
 * Create an order item entity to be shared between shipping module and Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderItemInterfaceBuilder extends AbstractSimpleObjectBuilder
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * OrderItemInterfaceBuilder constructor.
     * @param ObjectFactory $objectFactory
     * @param ModuleConfigInterface $config
     */
    public function __construct(
        ObjectFactory $objectFactory,
        ModuleConfigInterface $config
    ) {
        $this->config = $config;

        parent::__construct($objectFactory);
    }

    /**
     * @param RateRequest $rateRequest
     * @return void
     * @throws LocalizedException
     */
    public function setRateRequest(RateRequest $rateRequest)
    {
        $currencyCode = $rateRequest->getBaseCurrency();
        if ($currencyCode instanceof Currency) {
            $currencyCode = $currencyCode->getCurrencyCode();
        }

        $this->_set(OrderItemInterface::CURRENCY, $currencyCode);
    }

    /**
     * @param ItemInterface|\Magento\Quote\Model\Quote\Item\AbstractItem $quoteItem
     * @return void
     */
    public function setQuoteItem(ItemInterface $quoteItem)
    {
        $weightUom = $this->config->getWeightUnit($quoteItem->getQuote()->getStoreId());
        $dimensionsUom = ($weightUom === 'kgs') ? 'cm' : 'in';

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $quoteItem->getProduct()->getCategoryCollection();
        $categoryCollection->addNameToResult();
        $categoryNames = $categoryCollection->getColumnValues('name');

        $itemAmount = $quoteItem->getData('base_price');
        $itemAmount -= $quoteItem->getBaseDiscountAmount() / $quoteItem->getQty();

        $this->_set(OrderItemInterface::PRODUCT_ID, $quoteItem->getData('product_id'));
        $this->_set(OrderItemInterface::QTY, $quoteItem->getQty());
        $this->_set(OrderItemInterface::SKU, $quoteItem->getData('sku'));
        $this->_set(OrderItemInterface::NAME, $quoteItem->getData('name'));
        $this->_set(OrderItemInterface::DESCRIPTION, $quoteItem->getData('description'));
        $this->_set(OrderItemInterface::CATEGORIES, $categoryNames);
        $this->_set(OrderItemInterface::DIMENSIONS_UOM, $dimensionsUom);
        $this->_set(OrderItemInterface::LENGTH, $quoteItem->getProduct()->getData(SetupData::ATTRIBUTE_CODE_LENGTH));
        $this->_set(OrderItemInterface::WIDTH, $quoteItem->getProduct()->getData(SetupData::ATTRIBUTE_CODE_WIDTH));
        $this->_set(OrderItemInterface::HEIGHT, $quoteItem->getProduct()->getData(SetupData::ATTRIBUTE_CODE_HEIGHT));
        $this->_set(OrderItemInterface::WEIGHT_UOM, $weightUom);
        $this->_set(OrderItemInterface::WEIGHT, $quoteItem->getData('weight'));
        $this->_set(OrderItemInterface::AMOUNT, $itemAmount);
        $this->_set(OrderItemInterface::IS_FRAGILE, null);
        $this->_set(OrderItemInterface::IS_VIRTUAL, $quoteItem->getProduct()->getIsVirtual());
        $this->_set(OrderItemInterface::IS_PREPACKAGED, null);
        $this->_set(OrderItemInterface::CAN_ROTATE_VERTICAL, null);
        $this->_set(OrderItemInterface::COUNTRY_OF_ORIGIN, '');
        $this->_set(OrderItemInterface::COUNTRY_OF_MANUFACTURE, '');
        $this->_set(OrderItemInterface::ECCN, '');
        $this->_set(OrderItemInterface::SCHEDULE_B_INFO, '');
        $this->_set(OrderItemInterface::HS_CODE, '');
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     * @return void
     * @throws LocalizedException
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->_set(OrderItemInterface::CURRENCY, $order->getBaseCurrencyCode());
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return void
     * @throws LocalizedException
     */
    public function setOrderItem(\Magento\Sales\Api\Data\OrderItemInterface $orderItem)
    {
        $weightUom = $this->config->getWeightUnit($orderItem->getStoreId());
        $dimensionsUom = ($weightUom === 'kgs') ? 'cm' : 'in';

        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $orderItem->getProduct()->getCategoryCollection();
        $categoryCollection->addNameToResult();
        $categoryNames = $categoryCollection->getColumnValues('name');

        $itemAmount = $orderItem->getBasePrice();
        $itemAmount -= $orderItem->getBaseDiscountAmount() / $orderItem->getQtyOrdered();

        $this->_set(OrderItemInterface::PRODUCT_ID, $orderItem->getProductId());
        $this->_set(OrderItemInterface::QTY, $orderItem->getQtyOrdered());
        $this->_set(OrderItemInterface::SKU, $orderItem->getSku());
        $this->_set(OrderItemInterface::NAME, $orderItem->getName());
        $this->_set(OrderItemInterface::DESCRIPTION, $orderItem->getDescription());
        $this->_set(OrderItemInterface::CATEGORIES, $categoryNames);
        $this->_set(OrderItemInterface::DIMENSIONS_UOM, $dimensionsUom);
        $this->_set(OrderItemInterface::LENGTH, $orderItem->getProduct()->getData(SetupData::ATTRIBUTE_CODE_LENGTH));
        $this->_set(OrderItemInterface::WIDTH, $orderItem->getProduct()->getData(SetupData::ATTRIBUTE_CODE_WIDTH));
        $this->_set(OrderItemInterface::HEIGHT, $orderItem->getProduct()->getData(SetupData::ATTRIBUTE_CODE_HEIGHT));
        $this->_set(OrderItemInterface::WEIGHT_UOM, $weightUom);
        $this->_set(OrderItemInterface::WEIGHT, $orderItem->getWeight());
        $this->_set(OrderItemInterface::AMOUNT, $itemAmount);
        $this->_set(OrderItemInterface::IS_FRAGILE, null);
        $this->_set(OrderItemInterface::IS_VIRTUAL, $orderItem->getIsVirtual());
        $this->_set(OrderItemInterface::IS_PREPACKAGED, null);
        $this->_set(OrderItemInterface::CAN_ROTATE_VERTICAL, null);
        $this->_set(OrderItemInterface::COUNTRY_OF_ORIGIN, '');
        $this->_set(OrderItemInterface::COUNTRY_OF_MANUFACTURE, '');
        $this->_set(OrderItemInterface::ECCN, '');
        $this->_set(OrderItemInterface::SCHEDULE_B_INFO, '');
        $this->_set(OrderItemInterface::HS_CODE, '');
    }
}
