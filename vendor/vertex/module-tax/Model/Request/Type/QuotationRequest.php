<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request\Type;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\AddressInterface;
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
     * @param AddressInterface $taxAddress
     * @param int|null $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormattedRequestData(AddressInterface $taxAddress, $customerGroupId = null)
    {
        $date = $this->dateTime->date('Y-m-d');
        $taxLineItems = [];
        $taxLineItems = array_merge($taxLineItems, $this->getFormattedItemData($taxAddress, $customerGroupId));
        $taxLineItems[] = $this->lineItemShippingFormatter->getFormattedShippingLineItemData(
            $taxAddress,
            $customerGroupId
        );
        $taxLineItems = array_merge($taxLineItems, $this->getFormattedOrderGiftWrapData($taxAddress, $customerGroupId));
        $taxLineItems = array_merge(
            $taxLineItems,
            $this->getFormattedOrderPrintCardData($taxAddress, $customerGroupId)
        );

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
     * @param AddressInterface $taxAddress
     * @param int $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFormattedItemData(AddressInterface $taxAddress, $customerGroupId = null)
    {
        $giftWrappingEnabled = $this->moduleManager->isEnabled('Magento_GiftWrapping');

        $taxLineItems = [];

        /** @var \Magento\Quote\Model\Quote\Address\Item $taxAddressItem */
        foreach ($taxAddress->getAllVisibleItems() as $taxAddressItem) {
            if (!empty($taxAddressItem->getChildren()) && $taxAddressItem->isChildrenCalculated()) {
                foreach ($taxAddressItem->getChildren() as $child) {
                    $taxLineItems[] = $this->lineItemFormatter->getFormattedLineItemData(
                        $taxAddress,
                        $child,
                        $customerGroupId
                    );

                    if ($giftWrappingEnabled && $child->getData('gw_id')) {
                        $taxLineItems[] = $this->lineItemFormatter->getFormattedItemGiftWrapData(
                            $taxAddress,
                            $child,
                            $customerGroupId
                        );
                    }
                }
            } else {
                $taxLineItems[] = $this->lineItemFormatter->getFormattedLineItemData(
                    $taxAddress,
                    $taxAddressItem,
                    $customerGroupId
                );

                if ($giftWrappingEnabled && $taxAddressItem->getData('gw_id')) {
                    $taxLineItems[] = $this->lineItemFormatter->getFormattedItemGiftWrapData(
                        $taxAddress,
                        $taxAddressItem,
                        $customerGroupId
                    );
                }
            }
        }

        return $taxLineItems;
    }

    /**
     * Create properly formatted line item data of Order-level Giftwrapping for a Quote Request
     *
     * @param AddressInterface $taxAddress
     * @param int|null $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFormattedOrderGiftWrapData(AddressInterface $taxAddress, $customerGroupId = null)
    {
        if (!$taxAddress->getData('gw_id') || !$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return [];
        }

        return [$this->lineItemFormatter->getFormattedOrderGiftWrapData($taxAddress, $customerGroupId)];
    }

    /**
     * Create properly formatted line item data of Order-level Printed Cards for a Quote Request
     *
     * @param AddressInterface $taxAddress
     * @param int $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getFormattedOrderPrintCardData(AddressInterface $taxAddress, $customerGroupId = null)
    {
        if (!$taxAddress->getData('gw_add_card') || !$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return [];
        }

        return [$this->lineItemFormatter->getFormattedOrderPrintCardData($taxAddress, $customerGroupId)];
    }
}
