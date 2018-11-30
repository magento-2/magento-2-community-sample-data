<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Checkout\Orderline;

use Klarna\Core\Api\BuilderInterface;
use Klarna\Core\Helper\DataConverter;
use Klarna\Core\Helper\KlarnaConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditMemoItem;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Model\Store;
use Magento\Tax\Model\Calculation;
use Klarna\Core\Helper\GiftWrapping;

/**
 * Generate order gift wrapping item details
 */
class GiftWrap extends AbstractLine
{
    /**
     * gift wrap is not a total collector, it's a line item collector
     *
     * @var bool
     */
    protected $isTotalCollector = false;

    /**
     * @var string
     */
    private $itemType;

    /**
     * @var GiftWrapping
     */
    private $giftWrappingHelper;

    /**
     * GiftWrap constructor.
     *
     * @param DataConverter        $dataConverter
     * @param Calculation          $calculator
     * @param ScopeConfigInterface $config
     * @param DataObjectFactory    $dataObjectFactory
     * @param KlarnaConfig         $klarnaConfig
     * @param string               $itemType
     */
    public function __construct(
        DataConverter $dataConverter,
        Calculation $calculator,
        ScopeConfigInterface $config,
        DataObjectFactory $dataObjectFactory,
        KlarnaConfig $klarnaConfig,
        GiftWrapping $giftWrapping,
        $itemType = 'surcharge'
    ) {
        parent::__construct($dataConverter, $calculator, $config, $dataObjectFactory, $klarnaConfig);
        $this->itemType = $itemType;
        $this->giftWrappingHelper = $giftWrapping;
    }

    /**
     * Collect totals process.
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     * @throws \Klarna\Core\Exception
     */
    public function collect(BuilderInterface $checkout)
    {
        $object = $checkout->getObject();
        $store = $object->getStore();
        $giftWrapItem = [];
        $orderCreated = false;

        foreach ($object->getAllItems() as $item) {
            if ($item instanceof InvoiceItem || $item instanceof CreditMemoItem) {
                $orderCreated = true;
                $itemToProcess = $item->getOrderItem();
            }

            if ($item instanceof QuoteItem) {
                $itemToProcess = $item;
            }

            if (!$itemToProcess->getGwId()) {
                continue;
            }
            $giftWrapItem[] = $this->processItem($itemToProcess, $item, $store);
        }

        if ($orderCreated) {
            $object = $object->getOrder();
        }

        $item = $this->checkOrderForGiftwrap($object, $store);
        if ($item) {
            $giftWrapItem[] = $item;
        }

        $checkout->setGiftWrapItems($giftWrapItem);
        return $this;
    }

    /**
     * @param QuoteItem|Item                            $itemToProcess
     * @param QuoteItem|InvoiceItem|Item|CreditMemoItem $item
     * @param Store                                     $store
     * @return array
     * @throws \Klarna\Core\Exception
     */
    private function processItem($itemToProcess, $item, $store)
    {
        $_item = [
            'type'          => $this->getGwItemType(),
            'reference'     => substr(sprintf('%s - Gift Wrapping', $itemToProcess->getSku()), 0, 64),
            'name'          => (string)__('Gift Wrapping'),
            'quantity'      => (int)$item->getQty(),
            'discount_rate' => 0,
        ];
        if ($this->klarnaConfig->isSeparateTaxLine($store)) {
            $_item['tax_rate'] = 0;
            $_item['total_tax_amount'] = 0;
            $_item['unit_price'] = $this->helper->toApiFloat($itemToProcess->getGwBasePrice());
            $_item['total_amount'] = $this->helper->toApiFloat($itemToProcess->getGwBasePrice() * $item->getQty());
        } else {
            $taxRate = 0;
            if ($itemToProcess->getGwPrice() > 0) {
                $taxRate = ($itemToProcess->getTaxPercent() > 0) ? $itemToProcess->getTaxPercent()
                    : ($itemToProcess->getGwBaseTaxAmount() / $itemToProcess->getGwBasePrice() * 100);
            }

            $taxAmount = $itemToProcess->getGwBaseTaxAmount();
            /** @noinspection TypeUnsafeComparisonInspection */
            if ($taxAmount == 0) {
                $taxRate = 0;
            }
            $unitPrice = $itemToProcess->getGwBasePrice() + $taxAmount;

            $_item['tax_rate'] = $this->helper->toApiFloat($taxRate);
            $_item['total_tax_amount'] = $this->helper->toApiFloat($taxAmount * $item->getQty());
            $_item['unit_price'] = $this->helper->toApiFloat($unitPrice);
            $_item['total_amount'] = $this->helper->toApiFloat($unitPrice * $item->getQty());
        }
        return $_item;
    }

    /**
     * return order line type for gift wrapping
     *
     * @return string
     */
    public function getGwItemType()
    {
        return $this->itemType;
    }

    /**
     * @param Quote|Invoice|Creditmemo $object
     * @param Store                    $store
     * @return array
     * @throws \Klarna\Core\Exception
     */
    private function checkOrderForGiftwrap($object, $store)
    {
        if ($object->getGwId()) {
            //check gift wrap on quote/object level
            $store =  $object->getStore();
            $taxRate = $this->giftWrappingHelper->getGiftWrappingTaxRate($object, $store);

            $totalAmount = $object->getGwBasePrice() * ((100+$taxRate)/100);
            $taxAmount = $totalAmount * ($taxRate/(100+$taxRate));

            if ($this->klarnaConfig->isSeparateTaxLine($store)) {
                $taxRate = 0;
                $taxAmount = 0;
                $totalAmount = $object->getGwBasePrice();
            }
            $_item = [
                'type'             => $this->getGwItemType(),
                'reference'        => $object->getGwId(),
                'name'             => 'Gift Wrapping',
                'quantity'         => 1,
                'unit_price'       => $this->helper->toApiFloat($totalAmount),
                'tax_rate'         => $this->helper->toApiFloat($taxRate),
                'total_amount'     => $this->helper->toApiFloat($totalAmount),
                'total_tax_amount' => $this->helper->toApiFloat($taxAmount),
            ];

            return $_item;
        }
        return null;
    }

    /**
     * Add gift wrap to checkout request
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getGiftWrapItems()) {
            foreach ($checkout->getGiftWrapItems() as $item) {
                $checkout->addOrderLine($item);
            }
        }
        return $this;
    }
}
