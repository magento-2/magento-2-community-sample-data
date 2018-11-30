<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data;

use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Api\Data\QuoteDetailsInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Vertex\Data\LineItemInterface;
use Vertex\Services\Quote\RequestInterface;
use Vertex\Services\Quote\RequestInterfaceFactory;
use Vertex\Tax\Model\AddressDeterminer;
use Vertex\Tax\Model\Api\Utility\DeliveryTerm;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\DateTimeImmutableFactory;

/**
 * Builds a Quotation Request for the Vertex SDK
 */
class QuotationRequestBuilder
{
    const TRANSACTION_TYPE = 'SALE';

    /** @var AddressDeterminer */
    private $addressDeterminer;

    /** @var Config */
    private $config;

    /** @var CustomerBuilder */
    private $customerBuilder;

    /** @var DateTimeImmutableFactory */
    private $dateTimeFactory;

    /** @var DeliveryTerm */
    private $deliveryTerm;

    /** @var LineItemBuilder */
    private $lineItemBuilder;

    /** @var RequestInterfaceFactory */
    private $requestFactory;

    /** @var SellerBuilder */
    private $sellerBuilder;

    /**
     * @param LineItemBuilder $lineItemBuilder
     * @param RequestInterfaceFactory $requestFactory
     * @param CustomerBuilder $customerBuilder
     * @param SellerBuilder $sellerBuilder
     * @param Config $config
     * @param DeliveryTerm $deliveryTerm
     * @param DateTimeImmutableFactory $dateTimeFactory
     * @param AddressDeterminer $addressDeterminer
     */
    public function __construct(
        LineItemBuilder $lineItemBuilder,
        RequestInterfaceFactory $requestFactory,
        CustomerBuilder $customerBuilder,
        SellerBuilder $sellerBuilder,
        Config $config,
        DeliveryTerm $deliveryTerm,
        DateTimeImmutableFactory $dateTimeFactory,
        AddressDeterminer $addressDeterminer
    ) {
        $this->lineItemBuilder = $lineItemBuilder;
        $this->requestFactory = $requestFactory;
        $this->customerBuilder = $customerBuilder;
        $this->sellerBuilder = $sellerBuilder;
        $this->config = $config;
        $this->deliveryTerm = $deliveryTerm;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->addressDeterminer = $addressDeterminer;
    }

    /**
     * Create a properly formatted Quote Request for the Vertex API
     *
     * @param QuoteDetailsInterface $quoteDetails
     * @param string|null $scopeCode
     * @return RequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function buildFromQuoteDetails(QuoteDetailsInterface $quoteDetails, $scopeCode = null)
    {
        /** @var RequestInterface $request */
        $request = $this->requestFactory->create();
        $request->setDocumentDate($this->dateTimeFactory->create());
        $request->setTransactionType(static::TRANSACTION_TYPE);

        $taxLineItems = $this->getLineItemData($quoteDetails->getItems());
        $request->setLineItems($taxLineItems);

        $address = $this->addressDeterminer->determineAddress(
            $quoteDetails->getShippingAddress() ?: $quoteDetails->getBillingAddress(),
            $quoteDetails->getCustomerId(),
            $this->isVirtual($quoteDetails)
        );

        $seller = $this->sellerBuilder
            ->setScopeCode($scopeCode)
            ->setScopeType(ScopeInterface::SCOPE_STORE)
            ->build();

        $request->setSeller($seller);

        $taxClassKey = $quoteDetails->getCustomerTaxClassKey();
        if ($taxClassKey && $taxClassKey->getType() === TaxClassKeyInterface::TYPE_ID) {
            $customerTaxClassId = $quoteDetails->getCustomerTaxClassKey()->getValue();
        } else {
            $customerTaxClassId = $quoteDetails->getCustomerTaxClassId();
        }

        $request->setCustomer(
            $this->customerBuilder->buildFromCustomerAddress(
                $address,
                $quoteDetails->getCustomerId(),
                $customerTaxClassId,
                $scopeCode
            )
        );

        $this->deliveryTerm->addIfApplicable($request);

        if ($this->config->getLocationCode($scopeCode)) {
            $request->setLocationCode($this->config->getLocationCode($scopeCode));
        }

        return $request;
    }

    /**
     * Build Line Items for the Request
     *
     * @param QuoteDetailsItemInterface[] $items
     * @return LineItemInterface[]
     */
    private function getLineItemData(array $items)
    {
        // The resulting LineItemInterface[] to be used with Vertex
        $taxLineItems = [];

        // An array of codes for parent items
        $parentCodes = [];

        // A map of all items by their code
        $itemMap = [];

        // Item codes already processed - to prevent duplicates from bundles & configurables
        $processedItems = [];

        foreach ($items as $item) {
            $itemMap[$item->getCode()] = $item;
            if ($item->getParentCode()) {
                $parentCodes[] = $item->getParentCode();
            }
        }

        foreach ($items as $item) {
            if (in_array($item->getCode(), array_merge($parentCodes, $processedItems), true)) {
                // We merge these two arrays together as a convenience so we only need to run in_array once
                continue;
            }

            $quantity = $item->getParentCode()
                ? $item->getQuantity() * $itemMap[$item->getParentCode()]->getQuantity()
                : $item->getQuantity();

            $taxLineItems[] = $this->lineItemBuilder->buildFromQuoteDetailsItem($item, $quantity);
            $processedItems[] = $item->getCode();
        }

        return $taxLineItems;
    }

    /**
     * Determine if the Quote is virtual
     *
     * @param QuoteDetailsInterface $quoteDetails
     * @return bool
     */
    private function isVirtual(QuoteDetailsInterface $quoteDetails)
    {
        foreach ($quoteDetails->getItems() as $item) {
            if ($item->getType() === 'shipping') {
                return false;
            }
        }
        return true;
    }
}
