<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

/**
 * Contains information necessary for the Tax Invoice API calls
 *
 * This class will be removed in a future version as part of a larger refactoring effort.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class RequestItem
{
    /** @var ZipCodeFixer */
    private $zipCodeFixer;

    /** @var string */
    private $trustedId;

    /** @var string */
    private $requestType;

    /** @var string */
    private $documentDate;

    /** @var string */
    private $postingDate;

    /** @var string */
    private $transactionType;

    /** @var string */
    private $documentNumber;

    /** @var array */
    private $orderItems;

    /** @var string */
    private $locationCode;

    /** @var string */
    private $companyId;

    /** @var string */
    private $companyStreet1;

    /** @var string */
    private $companyStreet2;

    /** @var string */
    private $companyCity;

    /** @var string */
    private $companyCountry;

    /** @var string */
    private $companyState;

    /** @var string */
    private $companyPostcode;

    /** @var string */
    private $customerClass;

    /** @var string */
    private $customerCode;

    /** @var string */
    private $customerStreet1;

    /** @var string */
    private $customerStreet2;

    /** @var string */
    private $customerCity;

    /** @var string */
    private $customerRegion;

    /** @var string */
    private $customerPostcode;

    /** @var string */
    private $customerCountry;

    /**
     * @param ZipCodeFixer $zipCodeFixer
     */
    public function __construct(ZipCodeFixer $zipCodeFixer)
    {
        $this->zipCodeFixer = $zipCodeFixer;
    }

    /**
     * Export all information as an associated array for use with the SOAP API
     *
     * requestType: TaxAreaRequest, InvoiceRequest, QuotationRequest
     *
     * @return array
     */
    public function exportAsArray()
    {
        $request = [
            'Login' => [
                'TrustedId' => $this->getTrustedId()
            ],
            $this->getRequestType() => [
                'documentDate' => $this->getDocumentDate(),
                'postingDate' => $this->getPostingDate(),
                'transactionType' => $this->getTransactionType(),
                'documentNumber' => $this->getDocumentNumber(),
                'LineItem' => []
            ]
        ];

        if ($this->getDocumentNumber()) {
            $request[$this->getRequestType()]['documentNumber'] = $this->getDocumentNumber();
        }

        $orderItems = $this->getOrderItems();
        $request[$this->getRequestType()]['LineItem'] = $this->getFormattedItems($orderItems);

        return $request;
    }

    /**
     * Retrieve a SOAP Formatted associative array given item data
     *
     * @param array $items
     * @return array
     */
    public function getFormattedItems($items)
    {
        $queryItems = [];
        $i = 1;

        /**
         * lineItemNumber
         */
        foreach ($items as $key => $item) {
            /**
             * $key - quote_item_id
             */
            $tmpItem = [
                'lineItemNumber' => $i,
                'lineItemId' => $key,
                'locationCode' => $this->getLocationCode(),
                'Seller' => [
                    'Company' => $this->getCompanyId(),
                    'PhysicalOrigin' => [
                        'StreetAddress1' => $this->getCompanyStreet1(),
                        'StreetAddress2' => $this->getCompanyStreet2(),
                        'City' => $this->getCompanyCity(),
                        'Country' => $this->getCompanyCountry(),
                        'MainDivision' => $this->getCompanyState(),
                        'PostalCode' => $this->zipCodeFixer->fix($this->getCompanyPostcode()),
                    ]
                ],
                'Customer' => [
                    'CustomerCode' => [
                        'classCode' => $this->getCustomerClass(),
                        '_' => $this->getCustomerCode()
                    ],
                    'Destination' => [
                        'StreetAddress1' => $this->getCustomerStreet1(),
                        'StreetAddress2' => $this->getCustomerStreet2(),
                        'City' => $this->getCustomerCity(),
                        'MainDivision' => $this->getCustomerRegion(),
                        'PostalCode' => $this->zipCodeFixer->fix($this->getCustomerPostcode()),
                        'Country' => $this->getCustomerCountry()
                    ]
                ],
                'Product' => [
                    'productClass' => $item['product_class'],
                    '_' => substr($item['product_code'], 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED)
                ],
                'UnitPrice' => $item['price'],
                'Quantity' => $item['qty'],
                'ExtendedPrice' => $item['extended_price'],
            ];

            if ($this->getCustomerCountry() === 'CAN') {
                $tmpItem['deliveryTerm'] = 'SUP';
            }

            $queryItems[] = $tmpItem;
            $i++;
        }

        return $queryItems;
    }

    /**
     * Get the Trusted ID for the Request
     *
     * @return string
     */
    public function getTrustedId()
    {
        return $this->trustedId;
    }

    /**
     * Set the Trusted ID for the Request
     *
     * @param string $trustedId
     * @return RequestItem
     */
    public function setTrustedId($trustedId)
    {
        $this->trustedId = $trustedId;
        return $this;
    }

    /**
     * Get the Request Type
     *
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Set the Request Type
     *
     * @param string $requestType
     * @return RequestItem
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;
        return $this;
    }

    /**
     * Get the Document Date
     *
     * @return string
     */
    public function getDocumentDate()
    {
        return $this->documentDate;
    }

    /**
     * Set the Document Date
     *
     * @param string $documentDate
     * @return RequestItem
     */
    public function setDocumentDate($documentDate)
    {
        $this->documentDate = $documentDate;
        return $this;
    }

    /**
     * Get the posting date of the quote/order/invoice
     *
     * @return string
     */
    public function getPostingDate()
    {
        return $this->postingDate;
    }

    /**
     * Set the posting date of the quote/order/invoice
     *
     * @param string $postingDate
     * @return RequestItem
     */
    public function setPostingDate($postingDate)
    {
        $this->postingDate = $postingDate;
        return $this;
    }

    /**
     * Get the Transaction Type
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set the Transaction Type
     *
     * @param string $transactionType
     * @return RequestItem
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }

    /**
     * Get the Document Number
     *
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    /**
     * Set the Document Number
     *
     * @param string $documentNumber
     * @return RequestItem
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    /**
     * Get the Order Items for the Request
     *
     * @return array
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * Set the Order Items for the Request
     *
     * @param array $orderItems
     * @return RequestItem
     */
    public function setOrderItems(array $orderItems)
    {
        $this->orderItems = $orderItems;
        return $this;
    }

    /**
     * Get the Location Code to be used for the request
     *
     * @return string
     */
    public function getLocationCode()
    {
        return $this->locationCode;
    }

    /**
     * Set the Location Code to be used for the request
     *
     * @param string $locationCode
     * @return RequestItem
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;
        return $this;
    }

    /**
     * Get the Company ID
     *
     * @return string
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set the Company ID
     *
     * @param string $companyId
     * @return RequestItem
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * Get line 1 of the company street address
     *
     * @return string
     */
    public function getCompanyStreet1()
    {
        return $this->companyStreet1;
    }

    /**
     * Set line 1 of the company street address
     *
     * @param string $companyStreet1
     */
    public function setCompanyStreet1($companyStreet1)
    {
        $this->companyStreet1 = $companyStreet1;
    }

    /**
     * Get line 2 of the company street address
     *
     * @return string
     */
    public function getCompanyStreet2()
    {
        return $this->companyStreet2;
    }

    /**
     * Set line 2 of the company street address
     *
     * @param string $companyStreet2
     * @return RequestItem
     */
    public function setCompanyStreet2($companyStreet2)
    {
        $this->companyStreet2 = $companyStreet2;
        return $this;
    }

    /**
     * Get the city of the company address
     *
     * @return string
     */
    public function getCompanyCity()
    {
        return $this->companyCity;
    }

    /**
     * Set the city of the company address
     *
     * @param string $companyCity
     * @return RequestItem
     */
    public function setCompanyCity($companyCity)
    {
        $this->companyCity = $companyCity;
        return $this;
    }

    /**
     * Get the country of the company address
     *
     * @return string
     */
    public function getCompanyCountry()
    {
        return $this->companyCountry;
    }

    /**
     * Set the country of the company address
     *
     * @param string $companyCountry
     * @return RequestItem
     */
    public function setCompanyCountry($companyCountry)
    {
        $this->companyCountry = $companyCountry;
        return $this;
    }

    /**
     * Get the region of the company address
     *
     * @return string
     */
    public function getCompanyState()
    {
        return $this->companyState;
    }

    /**
     * Set the region of the company address
     *
     * @param string $companyState
     * @return RequestItem
     */
    public function setCompanyState($companyState)
    {
        $this->companyState = $companyState;
        return $this;
    }

    /**
     * Get the postcode of the company address
     *
     * @return string
     */
    public function getCompanyPostcode()
    {
        return $this->companyPostcode;
    }

    /**
     * Set the postcode of the company address
     *
     * @param string $companyPostcode
     * @return RequestItem
     */
    public function setCompanyPostcode($companyPostcode)
    {
        $this->companyPostcode = $companyPostcode;
        return $this;
    }

    /**
     * Get the Customer's Tax Class
     *
     * @return string
     */
    public function getCustomerClass()
    {
        return $this->customerClass;
    }

    /**
     * Set the Customer's Tax Class
     *
     * @param string $customerClass
     * @return RequestItem
     */
    public function setCustomerClass($customerClass)
    {
        $this->customerClass = $customerClass;
        return $this;
    }

    /**
     * Get the Customer's Code
     *
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->customerCode;
    }

    /**
     * Set the Customer's Code
     *
     * @param string $customerCode
     * @return RequestItem
     */
    public function setCustomerCode($customerCode)
    {
        $this->customerCode = $customerCode;
        return $this;
    }

    /**
     * Get line 1 of the Customer's Street Address
     *
     * @return string
     */
    public function getCustomerStreet1()
    {
        return $this->customerStreet1;
    }

    /**
     * Set line 1 of the Customer's Street Address
     *
     * @param string $customerStreet1
     * @return RequestItem
     */
    public function setCustomerStreet1($customerStreet1)
    {
        $this->customerStreet1 = $customerStreet1;
        return $this;
    }

    /**
     * Get line 2 of the Customer's Street Address
     *
     * @return string
     */
    public function getCustomerStreet2()
    {
        return $this->customerStreet2;
    }

    /**
     * Set line 2 of the Customer's Street Address
     *
     * @param string $customerStreet2
     * @return RequestItem
     */
    public function setCustomerStreet2($customerStreet2)
    {
        $this->customerStreet2 = $customerStreet2;
        return $this;
    }

    /**
     * Get the City of the Customer's Address
     *
     * @return string
     */
    public function getCustomerCity()
    {
        return $this->customerCity;
    }

    /**
     * Set the City of the Customer's Address
     *
     * @param string $customerCity
     * @return RequestItem
     */
    public function setCustomerCity($customerCity)
    {
        $this->customerCity = $customerCity;
        return $this;
    }

    /**
     * Get the Region of the Customer's Address
     *
     * @return string
     */
    public function getCustomerRegion()
    {
        return $this->customerRegion;
    }

    /**
     * Set the Region of the Customer's Address
     *
     * @param string $customerRegion
     * @return RequestItem
     */
    public function setCustomerRegion($customerRegion)
    {
        $this->customerRegion = $customerRegion;
        return $this;
    }

    /**
     * Get the Postcode of the Customer's Address
     *
     * @return string
     */
    public function getCustomerPostcode()
    {
        return $this->customerPostcode;
    }

    /**
     * Set the Postcode of the Customer's Address
     *
     * @param string $customerPostcode
     * @return RequestItem
     */
    public function setCustomerPostcode($customerPostcode)
    {
        $this->customerPostcode = $customerPostcode;
        return $this;
    }

    /**
     * Get the Country of the Customer's Address
     *
     * @return string
     */
    public function getCustomerCountry()
    {
        return $this->customerCountry;
    }

    /**
     * Set the Country of the Customer's Address
     *
     * @param string $customerCountry
     * @return RequestItem
     */
    public function setCustomerCountry($customerCountry)
    {
        $this->customerCountry = $customerCountry;
        return $this;
    }
}
