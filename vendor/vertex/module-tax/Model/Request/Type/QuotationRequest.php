<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request\Type;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Model\Quote\Address;
use Vertex\Tax\Model\ModuleManager;
use Vertex\Tax\Model\Request;
use Vertex\Tax\Model\Request\LineItem;

/**
 * Assembles the entire format of a Quotation Request for the Vertex API
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuotationRequest
{
    const TRANSACTION_TYPE = 'SALE';
    const REQUEST_TYPE = 'QuotationRequest';

    /** @var LineItem */
    private $lineItemFormatter;

    /** @var Request\Header */
    private $requestHeaderFormatter;

    /** @var Request\Shipping */
    private $lineItemShippingFormatter;

    /** @var ModuleManager */
    private $moduleManager;

    /** @var DateTime */
    private $dateTime;

    /**
     * @param ModuleManager $moduleManager
     * @param LineItem $lineItemFormatter
     * @param Request\Header $requestHeaderFormatter
     * @param Request\Shipping $lineItemShippingFormatter
     * @param DateTime $dateTime
     */
    public function __construct(
        ModuleManager $moduleManager,
        LineItem $lineItemFormatter,
        Request\Header $requestHeaderFormatter,
        Request\Shipping $lineItemShippingFormatter,
        DateTime $dateTime
    ) {
        $this->moduleManager = $moduleManager;
        $this->lineItemFormatter = $lineItemFormatter;
        $this->requestHeaderFormatter = $requestHeaderFormatter;
        $this->lineItemShippingFormatter = $lineItemShippingFormatter;
        $this->dateTime = $dateTime;
    }

    /**
     * Create a properly formatted Quote Request for the Vertex API
     *
     * @param Address $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedRequestData(Address $taxAddress)
    {
        $date = $this->dateTime->date('Y-m-d');
        $taxLineItems = [];

        $taxLineItems = array_merge($taxLineItems, $this->getFormattedItemData($taxAddress));

        $taxLineItems[] = $this->lineItemShippingFormatter->getFormattedShippingLineItemData($taxAddress);

        $taxLineItems = array_merge($taxLineItems, $this->getFormattedOrderGiftWrapData($taxAddress));
        $taxLineItems = array_merge($taxLineItems, $this->getFormattedOrderPrintCardData($taxAddress));

        $request = $this->requestHeaderFormatter->getFormattedHeaderData();
        $request[static::REQUEST_TYPE] = [
            'documentDate' => $date,
            'postingDate' => $date,
            'transactionType' => static::TRANSACTION_TYPE,
            'LineItem' => $taxLineItems
        ];

        return $request;
    }

    /**
     * Create properly formatted line item data for a Quote Request
     *
     * @param $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFormattedItemData($taxAddress)
    {
        $giftWrappingEnabled = $this->moduleManager->isEnabled('Magento_GiftWrapping');

        $taxLineItems = [];

        /** @var \Magento\Quote\Model\Quote\Address\Item $taxAddressItem */
        foreach ($taxAddress->getAllVisibleItems() as $taxAddressItem) {
            if (!empty($taxAddressItem->getChildren()) && $taxAddressItem->isChildrenCalculated()) {
                foreach ($taxAddressItem->getChildren() as $child) {
                    $taxLineItems[] = $this->lineItemFormatter->getFormattedLineItemData($taxAddress, $child);

                    if ($giftWrappingEnabled && $child->getData('gw_id')) {
                        $taxLineItems[] = $this->lineItemFormatter->getFormattedItemGiftWrapData($taxAddress, $child);
                    }
                }
            } else {
                $taxLineItems[] = $this->lineItemFormatter->getFormattedLineItemData($taxAddress, $taxAddressItem);

                if ($giftWrappingEnabled && $taxAddressItem->getData('gw_id')) {
                    $taxLineItems[] = $this->lineItemFormatter->getFormattedItemGiftWrapData(
                        $taxAddress,
                        $taxAddressItem
                    );
                }
            }
        }

        return $taxLineItems;
    }

    /**
     * Create properly formatted line item data of Order-level Giftwrapping for a Quote Request
     *
     * @param Address $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFormattedOrderGiftWrapData($taxAddress)
    {
        if (!$taxAddress->getData('gw_id') || !$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return [];
        }

        return [$this->lineItemFormatter->getFormattedOrderGiftWrapData($taxAddress)];
    }

    /**
     * Create properly formatted line item data of Order-level Printed Cards for a Quote Request
     *
     * @param Address $taxAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFormattedOrderPrintCardData($taxAddress)
    {
        if (!$taxAddress->getData('gw_add_card') || !$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return [];
        }

        return [$this->lineItemFormatter->getFormattedOrderPrintCardData($taxAddress)];
    }
}
