<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Customer\Api\Data\AddressInterface as CustomerAddress;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
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
     * @param CustomerAddress $taxAddress
     * @return bool|\Magento\Framework\DataObject
     * @throws NoSuchEntityException
     */
    public function calculateTaxAreaIds(CustomerAddress $taxAddress)
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

        $response = $this->taxAreaRequest->taxAreaLookup(
            $address
        );

        if ($response === false) {
            return false;
        }
        return $response->getTaxAreaWithHighestConfidence();
    }

    /**
     * Retrieve a Quotation Response given a Quote Address
     *
     * @param Address $taxAddress
     * @return TaxQuoteResponse|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws NoSuchEntityException
     */
    public function calculateTax(Address $taxAddress)
    {
        $request = $this->quotationRequestFormatter->getFormattedRequestData($taxAddress);

        /* Send API Request */
        $response = $this->taxQuoteRequest->taxQuote($request);

        if (!$response) {
            return false;
        }

        /* Process response */
        return $this->taxQuoteResponse->parseResponse($response);
    }
}
