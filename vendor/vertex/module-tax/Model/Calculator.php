<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Vertex\Tax\Exception\ApiRequestException;
use Vertex\Tax\Model\TaxArea\TaxAreaRequest;
use Vertex\Tax\Model\TaxQuote\TaxQuoteRequest;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;

/**
 * Performs Tax Calculation Requests
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Calculator
{
    /** @var Request\Address */
    private $addressFormatter;

    /** @var Request\Type\QuotationRequest */
    private $quotationRequestFormatter;

    /** @var TaxAreaRequest */
    private $taxAreaRequest;

    /** @var TaxQuoteRequest */
    private $taxQuoteRequest;

    /** @var TaxQuoteResponse */
    private $taxQuoteResponse;

    /**
     * @param Request\Address $addressFormatter
     * @param Request\Type\QuotationRequest $quotationRequestFormatter
     * @param TaxAreaRequest $taxAreaRequest
     * @param TaxQuoteRequest $taxQuoteRequest
     * @param TaxQuoteResponse $taxQuoteResponse
     */
    public function __construct(
        Request\Address $addressFormatter,
        Request\Type\QuotationRequest $quotationRequestFormatter,
        TaxAreaRequest $taxAreaRequest,
        TaxQuoteRequest $taxQuoteRequest,
        TaxQuoteResponse $taxQuoteResponse
    ) {
        $this->addressFormatter = $addressFormatter;
        $this->quotationRequestFormatter = $quotationRequestFormatter;
        $this->taxAreaRequest = $taxAreaRequest;
        $this->taxQuoteRequest = $taxQuoteRequest;
        $this->taxQuoteResponse = $taxQuoteResponse;
    }

    /**
     * Retrieve the tax area IDs for a Customer Address
     *
     * @param QuoteAddressInterface $taxAddress
     * @param string|null $store
     * @return bool|\Magento\Framework\DataObject
     * @throws NoSuchEntityException
     */
    public function calculateTaxAreaIds(QuoteAddressInterface $taxAddress, $store = null)
    {
        $street = $taxAddress->getStreet();

        $address = $this->addressFormatter->getFormattedAddressData(
            $street,
            $taxAddress->getCity(),
            $taxAddress->getRegionId(),
            $taxAddress->getPostcode(),
            $taxAddress->getCountryId()
        );

        if ($address['Country'] !== 'USA') {
            return true;
        }

        try {
            $response = $this->taxAreaRequest->taxAreaLookup($address, $store);
        } catch (ApiRequestException $e) {
            return false;
        }

        return $response->getTaxAreaWithHighestConfidence();
    }

    /**
     * Retrieve a Quotation Response given a Quote Address
     *
     * @param QuoteAddressInterface $taxAddress
     * @param int|null $customerGroupId
     * @return TaxQuoteResponse|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws NoSuchEntityException
     */
    public function calculateTax(QuoteAddressInterface $taxAddress, $customerGroupId = null)
    {
        $request = $this->quotationRequestFormatter->getFormattedRequestData($taxAddress, $customerGroupId);

        /* Send API Request */
        $response = $this->taxQuoteRequest->taxQuote($request);

        if (!$response) {
            return false;
        }

        /* Process response */
        return $this->taxQuoteResponse->parseResponse($response);
    }
}
