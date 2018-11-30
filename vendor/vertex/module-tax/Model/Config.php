<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\Config as TaxConfig;

/**
 * Configuration retrieval tool
 */
class Config
{
    /**
     * @var string
     * @deprecated This will be removed in the near future as we stop using a calculation method to determine if enabled
     */
    const CALC_UNIT_VERTEX = 'VERTEX_UNIT_BASE_CALCULATION';

    const CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE = 'tax/classes/default_customer_code';
    const CONFIG_XML_PATH_DEFAULT_TAX_CALCULATION_ADDRESS_TYPE = 'tax/calculation/based_on';
    const CONFIG_XML_PATH_ENABLE_VERTEX = 'tax/vertex_settings/enable_vertex';
    const CONFIG_XML_PATH_PRINTED_CARD_PRICE = 'sales/gift_options/printed_card_price';
    const CONFIG_XML_PATH_SHIPPING_TAX_CLASS = 'tax/classes/shipping_tax_class';
    const CONFIG_XML_PATH_TAX_APPLY_ON = 'tax/calculation/apply_tax_on';
    const CONFIG_XML_PATH_TAX_DISPLAY_IN_CATALOG = 'tax/display/type';

    const CONFIG_XML_PATH_VERTEX_API_KEY = 'tax/vertex_settings/password';
    const CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID = 'tax/vertex_settings/trustedId';
    const CONFIG_XML_PATH_VERTEX_API_USER = 'tax/vertex_settings/login';
    const CONFIG_XML_PATH_VERTEX_CITY = 'tax/vertex_seller_info/city';
    const CONFIG_XML_PATH_VERTEX_COMPANY_CODE = 'tax/vertex_seller_info/company';
    const CONFIG_XML_PATH_VERTEX_COUNTRY = 'tax/vertex_seller_info/country_id';
    const CONFIG_XML_PATH_VERTEX_INVOICE_DATE = 'tax/vertex_settings/invoice_tax_date';

    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER = 'tax/vertex_settings/invoice_order';

    const CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS = 'tax/vertex_settings/invoice_order_status';
    const CONFIG_XML_PATH_VERTEX_LOCATION_CODE = 'tax/vertex_seller_info/location_code';
    const CONFIG_XML_PATH_VERTEX_POSTAL_CODE = 'tax/vertex_seller_info/postalCode';
    const CONFIG_XML_PATH_VERTEX_REGION = 'tax/vertex_seller_info/region_id';
    const CONFIG_XML_PATH_VERTEX_STREET1 = 'tax/vertex_seller_info/streetAddress1';
    const CONFIG_XML_PATH_VERTEX_STREET2 = 'tax/vertex_seller_info/streetAddress2';

    const MAX_CHAR_PRODUCT_CODE_ALLOWED = 40;
    const VALUE_APPLY_ON_CUSTOM = 0;
    const VALUE_APPLY_ON_ORIGINAL_ONLY = 1;
    const VERTEX_ADDRESS_API_HOST = 'tax/vertex_settings/address_api_url';
    const VERTEX_API_HOST = 'tax/vertex_settings/api_url';
    const VERTEX_CREDITMEMO_ADJUSTMENT_CLASS = 'tax/classes/creditmemo_adjustment_class';
    const VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE = 'tax/classes/creditmemo_adjustment_negative_code';
    const VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE = 'tax/classes/creditmemo_adjustment_positive_code';
    const VERTEX_GIFTWRAP_ITEM_CLASS = 'tax/classes/giftwrap_item_class';
    const VERTEX_GIFTWRAP_ITEM_CODE_PREFIX = 'tax/classes/giftwrap_item_code';
    const VERTEX_GIFTWRAP_ORDER_CLASS = 'tax/classes/giftwrap_order_class';
    const VERTEX_GIFTWRAP_ORDER_CODE = 'tax/classes/giftwrap_order_code';
    const VERTEX_PRINTED_GIFTCARD_CLASS = 'tax/classes/printed_giftcard_class';
    const VERTEX_PRINTED_GIFTCARD_CODE = 'tax/classes/printed_giftcard_code';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve which price we should be applying tax to
     *
     * @param null $store
     * @param string $scope
     * @return string
     */
    public function getApplyTaxOn($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_TAX_APPLY_ON, $store, $scope);
    }

    /**
     * Get the City of the Company Address
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCompanyCity($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_CITY, $store, $scope);
    }

    /**
     * Retrieve the Company Code
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCompanyCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_COMPANY_CODE, $store, $scope);
    }

    /**
     * Get the Country of the Company Address
     *
     * @param string|null $store
     * @param string $scope
     * @return bool|float|null
     */
    public function getCompanyCountry($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $country = $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_COUNTRY, $store, $scope);
        return $country !== null ? $country : false;
    }

    /**
     * Get the Postal Code of the Company Address
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCompanyPostalCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_POSTAL_CODE, $store, $scope);
    }

    /**
     * Get the Region ID of the Company Address
     *
     * @param string|null $store
     * @param string $scope
     * @return bool|float|null
     */
    public function getCompanyRegionId($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $region = $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_REGION, $store, $scope);
        return $region !== null ? $region : false;
    }

    /**
     * Get Line 1 of the Company Street Address
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCompanyStreet1($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_STREET1, $store, $scope);
    }

    /**
     * Get Line 2 of the Company Street Address
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCompanyStreet2($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_STREET2, $store, $scope);
    }

    /**
     * Retrieve a value from the configuration within a scope
     *
     * @param string $value
     * @param string|null $scopeId
     * @param string|null $scope
     * @return mixed
     */
    public function getConfigValue($value, $scopeId = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($value, $scope, $scopeId);
    }

    /**
     * Get the Tax class for a creditmemo adjustment fee
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCreditmemoAdjustmentFeeClass($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, $store, $scope);
    }

    /**
     * Get the code for a creditmemo adjustment fee
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCreditmemoAdjustmentFeeCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_NEGATIVE_CODE, $store, $scope);
    }

    /**
     * Get the tax class for a positive adjustment on a creditmemo
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCreditmemoAdjustmentPositiveClass($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_CLASS, $store, $scope);
    }

    /**
     * Get the positive adjustment code for a creditmemo
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getCreditmemoAdjustmentPositiveCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_CREDITMEMO_ADJUSTMENT_POSITIVE_CODE, $store, $scope);
    }

    /**
     * Get the Default Customer Code
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getDefaultCustomerCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_DEFAULT_CUSTOMER_CODE, $store, $scope);
    }

    /**
     * Get the Tax Class for Item-level Giftwrapping
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getGiftWrappingItemClass($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ITEM_CLASS, $store, $scope);
    }

    /**
     * Get the code prefix for Item-level Giftwrapping
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getGiftWrappingItemCodePrefix($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ITEM_CODE_PREFIX, $store, $scope);
    }

    /**
     * Get the Tax Class for Order-level Giftwrapping
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getGiftWrappingOrderClass($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ORDER_CLASS, $store, $scope);
    }

    /**
     * Get the code for Order-level Giftwrapping
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getGiftWrappingOrderCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_GIFTWRAP_ORDER_CODE, $store, $scope);
    }

    /**
     * Retrieve the Location Code
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getLocationCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_LOCATION_CODE, $store, $scope);
    }

    /**
     * Retrieve the price of a Printed Gift Card
     *
     * @param string|null $store
     * @param string $scope
     * @return mixed
     */
    public function getPrintedCardPrice($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(
            self::CONFIG_XML_PATH_PRINTED_CARD_PRICE,
            $store,
            $scope
        );
    }

    /**
     * Get the Tax Class for a Printed Gift Card
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getPrintedGiftcardClass($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_PRINTED_GIFTCARD_CLASS, $store, $scope);
    }

    /**
     * Get the code for a Printed Gift Card
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getPrintedGiftcardCode($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_PRINTED_GIFTCARD_CODE, $store, $scope);
    }

    /**
     * Get the Tax Class ID to be used for Shipping
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getShippingTaxClassId($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store, $scope);
    }

    /**
     * Get the Trusted ID for the Vertex Integration
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getTrustedId($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_API_TRUSTED_ID, $store, $scope);
    }

    /**
     * Get the URL of the Tax Area Lookup API Endpoint
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getVertexAddressHost($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_ADDRESS_API_HOST, $store, $scope);
    }

    /**
     * Get the URL of the Quotation and Invoicing API Endpoint
     *
     * @param string|null $store
     * @param string $scope
     * @return float|null
     */
    public function getVertexHost($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::VERTEX_API_HOST, $store, $scope);
    }

    /**
     * Grab the Order Status during which we should commit to the Tax Log
     *
     * @param string|null $store
     * @param string $scope
     * @return string
     */
    public function invoiceOrderStatus($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER_STATUS, $store, $scope);
    }

    /**
     * Determine whether or not tax is turned on to display in the catalog
     *
     * @param string|null $store
     * @param string $scope
     * @return bool
     */
    public function isDisplayPriceInCatalogEnabled($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $configValue = $this->getConfigValue(self::CONFIG_XML_PATH_TAX_DISPLAY_IN_CATALOG, $store, $scope);
        return (int)$configValue !== TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Determine if Vertex has been enabled
     *
     * @param string|null $scopeId
     * @param string $scope
     * @return bool
     */
    public function isVertexActive($scopeId = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        if ($this->getConfigValue(self::CONFIG_XML_PATH_ENABLE_VERTEX, $scopeId, $scope)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if we commit to the Tax Log during Invoice Creation or not
     *
     * @param string|null $store
     * @param string $scope
     * @return bool
     */
    public function requestByInvoiceCreation($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $vertexInvoiceEvent = $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER, $store, $scope);

        return $vertexInvoiceEvent === 'invoice_created';
    }

    /**
     * Determine if we commit to the Tax Log during an Order Status change or not
     *
     * @param string|null $store
     * @param string $scope
     * @return bool
     */
    public function requestByOrderStatus($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $vertexInvoiceEvent = $this->getConfigValue(self::CONFIG_XML_PATH_VERTEX_INVOICE_ORDER, $store, $scope);

        return $vertexInvoiceEvent === 'order_status';
    }
}
