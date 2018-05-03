<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Configuration retrieval tool
 */
class Config
{
    const CONFIG_XML_PATH_ENABLE_VERTEX = 'tax/vertex_settings/enable_vertex';

    const CONFIG_XML_PATH_DEFAULT_TAX_CALCULATION_ADDRESS_TYPE = 'tax/calculation/based_on';

    const CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE = 'tax/classes/default_customer_code';

    const VERTEX_API_HOST = 'tax/vertex_settings/api_url';

    const CONFIG_XML_PATH_VERTEX_API_USER = 'tax/vertex_settings/login';

    const CONFIG_XML_PATH_VERTEX_API_KEY = 'tax/vertex_settings/password';

    const CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID = 'tax/vertex_settings/trustedId';

    const CONFIG_XML_PATH_VERTEX_COMPANY_CODE = 'tax/vertex_seller_info/company';

    const CONFIG_XML_PATH_VERTEX_LOCATION_CODE = 'tax/vertex_seller_info/location_code';

    const CONFIG_XML_PATH_VERTEX_STREET1 = 'tax/vertex_seller_info/streetAddress1';

    const CONFIG_XML_PATH_VERTEX_STREET2 = 'tax/vertex_seller_info/streetAddress2';

    const CONFIG_XML_PATH_VERTEX_CITY = 'tax/vertex_seller_info/city';

    const CONFIG_XML_PATH_VERTEX_COUNTRY = 'tax/vertex_seller_info/country_id';

    const CONFIG_XML_PATH_VERTEX_REGION = 'tax/vertex_seller_info/region_id';

    const CONFIG_XML_PATH_VERTEX_POSTAL_CODE = 'tax/vertex_seller_info/postalCode';

    const CONFIG_XML_PATH_VERTEX_INVOICE_DATE = 'tax/vertex_settings/invoice_tax_date';

    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER = 'tax/vertex_settings/invoice_order';

    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS = 'tax/vertex_settings/invoice_order_status';

    const CONFIG_XML_PATH_SHIPPING_TAX_CLASS = 'tax/classes/shipping_tax_class';

    const VERTEX_ADDRESS_API_HOST = 'tax/vertex_settings/address_api_url';

    const VERTEX_CREDITMEMO_ADJUSTMENT_CLASS = 'tax/classes/creditmemo_adjustment_class';

    const VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE = 'tax/classes/creditmemo_adjustment_negative_code';

    const VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE = 'tax/classes/creditmemo_adjustment_positive_code';

    const VERTEX_GIFTWRAP_ORDER_CLASS = 'tax/classes/giftwrap_order_class';

    const VERTEX_GIFTWRAP_ORDER_CODE = 'tax/classes/giftwrap_order_code';

    const VERTEX_GIFTWRAP_ITEM_CLASS = 'tax/classes/giftwrap_item_class';

    const VERTEX_GIFTWRAP_ITEM_CODE_PREFIX = 'tax/classes/giftwrap_item_code';

    const CONFIG_XML_PATH_PRINTED_CARD_PRICE = 'sales/gift_options/printed_card_price';

    const VERTEX_PRINTED_GIFTCARD_CLASS = 'tax/classes/printed_giftcard_class';

    const VERTEX_PRINTED_GIFTCARD_CODE = 'tax/classes/printed_giftcard_code';

    const CONFIG_XML_PATH_VERTEX_ALLOW_CART_QUOTE = 'tax/vertex_settings/allow_cart_request';

    const CONFIG_XML_PATH_VERTEX_SHOW_MANUAL_BUTTON = 'tax/vertex_settings/show_manual_button';

    const CONFIG_XML_PATH_VERTEX_SHOW_POPUP = 'tax/vertex_settings/show_taxrequest_popup';

    const CONFIG_XML_PATH_TAX_APPLY_ON = 'tax/calculation/apply_tax_on';
    const VALUE_APPLY_ON_ORIGINAL_ONLY = 1;
    const VALUE_APPLY_ON_CUSTOM = 0;

    const VERTEX_CALCULATION_FUNCTION = 'tax/vertex_settings/calculation_function';

    const VERTEX_VALIDATION_FUNCTION = 'tax/vertex_settings/valadtion_function';

    const MAX_CHAR_PRODUCT_CODE_ALLOWED = 40;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Determine if Vertex has been enabled
     *
     * @param string|null $store
     * @return bool
     */
    public function isVertexActive($store = null)
    {
        if ($this->getConfigValue(self::CONFIG_XML_PATH_ENABLE_VERTEX, $store)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve the Location Code
     *
     * @param string|null $store
     * @return float|null
     */
    public function getLocationCode($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_LOCATION_CODE, $store);
    }

    /**
     * Retrieve the Company Code
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCompanyCode($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_COMPANY_CODE, $store);
    }

    /**
     * Get Line 1 of the Company Street Address
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCompanyStreet1($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_STREET1, $store);
    }

    /**
     * Get Line 2 of the Company Street Address
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCompanyStreet2($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_STREET2, $store);
    }

    /**
     * Get the City of the Company Address
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCompanyCity($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_CITY, $store);
    }

    /**
     * Get the Country of the Company Address
     *
     * @param string|null $store
     * @return bool|float|null
     */
    public function getCompanyCountry($store = null)
    {
        return ('null' !== $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_COUNTRY, $store) ? $this->getConfigValue(
            self::CONFIG_XML_PATH_VERTEX_COUNTRY,
            $store
        ) : false);
    }

    /**
     * Get the Region ID of the Company Address
     *
     * @param string|null $store
     * @return bool|float|null
     */
    public function getCompanyRegionId($store = null)
    {
        return ('null' !== $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_REGION, $store) ? $this->getConfigValue(
            self::CONFIG_XML_PATH_VERTEX_REGION,
            $store
        ) : false);
    }

    /**
     * Get the Postal Code of the Company Address
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCompanyPostalCode($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_POSTAL_CODE, $store);
    }

    /**
     * Get the Tax Class ID to be used for Shipping
     *
     * @param string|null $store
     * @return float|null
     */
    public function getShippingTaxClassId($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
    }

    /**
     * Get the Trusted ID for the Vertex Integration
     *
     * @param string|null $store
     * @return float|null
     */
    public function getTrustedId($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID, $store);
    }

    /**
     * Get the URL of the Quotation and Invoicing API Endpoint
     *
     * @param string|null $store
     * @return float|null
     */
    public function getVertexHost($store = null)
    {
        return $this->getConfigValue(self::VERTEX_API_HOST, $store);
    }

    /**
     * Get the URL of the Tax Area Lookup API Endpoint
     *
     * @param string|null $store
     * @return float|null
     */
    public function getVertexAddressHost($store = null)
    {
        return $this->getConfigValue(self::VERTEX_ADDRESS_API_HOST, $store);
    }

    /**
     * Get the Default Customer Code
     *
     * @param string|null $store
     * @return float|null
     */
    public function getDefaultCustomerCode($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE, $store);
    }

    /**
     * Get the code for a creditmemo adjustment fee
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCreditmemoAdjustmentFeeCode($store = null)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE, $store);
    }

    /**
     * Get the Tax class for a creditmemo adjustment fee
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCreditmemoAdjustmentFeeClass($store = null)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, $store);
    }

    /**
     * Get the positive adjustment code for a creditmemo
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCreditmemoAdjustmentPositiveCode($store = null)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE, $store);
    }

    /**
     * Get the tax class for a positive adjustment on a creditmemo
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCreditmemoAdjustmentPositiveClass($store = null)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, $store);
    }

    /**
     * Retrieve whether or not we can call the Vertex API from the Cart
     *
     * @param string|null $store
     * @return float|null
     */
    public function allowCartQuote($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_ALLOW_CART_QUOTE, $store);
    }

    /**
     * Get the Tax Class for Order-level Giftwrapping
     *
     * @param string|null $store
     * @return float|null
     */
    public function getGiftWrappingOrderClass($store = null)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ORDER_CLASS, $store);
    }

    /**
     * Get the code for Order-level Giftwrapping
     *
     * @param string|null $store
     * @return float|null
     */
    public function getGiftWrappingOrderCode($store = null)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ORDER_CODE, $store);
    }

    /**
     * Get the Tax Class for Item-level Giftwrapping
     *
     * @param string|null $store
     * @return float|null
     */
    public function getGiftWrappingItemClass($store = null)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ITEM_CLASS, $store);
    }

    /**
     * Get the code prefix for Item-level Giftwrapping
     *
     * @param string|null $store
     * @return float|null
     */
    public function getGiftWrappingItemCodePrefix($store = null)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ITEM_CODE_PREFIX, $store);
    }

    /**
     * Get the Tax Class for a Printed Gift Card
     *
     * @param string|null $store
     * @return float|null
     */
    public function getPrintedGiftcardClass($store = null)
    {
        return $this->getConfigValue(self::VERTEX_PRINTED_GIFTCARD_CLASS, $store);
    }

    /**
     * Get the Tax Calculation function
     *
     * @param string|null $store
     * @return float|null
     */
    public function getCalculationFunction($store = null)
    {
        return $this->getConfigValue(self::VERTEX_CALCULATION_FUNCTION, $store);
    }

    /**
     * Get the Address Validation function
     *
     * @param string|null $store
     * @return float|null
     */
    public function getValidationFunction($store = null)
    {
        return $this->getConfigValue(self::VERTEX_VALIDATION_FUNCTION, $store);
    }

    /**
     * Get the code for a Printed Gift Card
     *
     * @param string|null $store
     * @return float|null
     */
    public function getPrintedGiftcardCode($store = null)
    {
        return $this->getConfigValue(self::VERTEX_PRINTED_GIFTCARD_CODE, $store);
    }

    /**
     * Determine if we commit to the Tax Log during Invoice Creation or not
     *
     * @param string|null $store
     * @return bool
     */
    public function requestByInvoiceCreation($store = null)
    {
        $vertexInvoiceEvent = $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER, $store);

        return $vertexInvoiceEvent === 'invoice_created';
    }

    /**
     * Determine if we commit to the Tax Log during an Order Status change or not
     *
     * @param string|null $store
     * @return bool
     */
    public function requestByOrderStatus($store = null)
    {
        $vertexInvoiceEvent = $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER, $store);

        return $vertexInvoiceEvent === 'order_status';
    }

    /**
     * Grab the Order Status during which we should commit to the Tax Log
     *
     * @param string|null $store
     * @return string
     */
    public function invoiceOrderStatus($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS, $store);
    }

    /**
     * Determine if we should show the Manual Invoice Button
     *
     * @return bool
     */
    public function shouldShowManualButton()
    {
        return (bool)$this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_SHOW_MANUAL_BUTTON);
    }

    /**
     * Retrieve which price we should be applying tax to
     *
     * @return string
     */
    public function getApplyTaxOn($store = null)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_TAX_APPLY_ON, $store);
    }

    /**
     * Retrieve a value from the configuration within a scope
     *
     * @param string $value
     * @param string|null $store
     * @return mixed
     */
    public function getConfigValue($value, $store = null)
    {
        $value = $this->scopeConfig->getValue($value, ScopeInterface::SCOPE_STORE, $store);

        return $value;
    }

    /**
     * Retrieve the price of a Printed Gift Card
     *
     * @param string|null $store
     * @return mixed
     */
    public function getPrintedCardPrice($store = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_PRINTED_CARD_PRICE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
