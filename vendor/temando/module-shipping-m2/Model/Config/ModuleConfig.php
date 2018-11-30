<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Config;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Module\ModuleList;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando Config Values Handler
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ModuleConfig implements ModuleConfigInterface, WsConfigInterface
{
    const CONFIG_XML_PATH_CHECKOUT_ENABLED = 'carriers/temando/active';
    const CONFIG_XML_PATH_LOGGING_ENABLED = 'carriers/temando/logging_enabled';

    const CONFIG_XML_PATH_SESSION_ENDPOINT = 'carriers/temando/session_endpoint';
    const CONFIG_XML_PATH_API_ENDPOINT = 'carriers/temando/sovereign_endpoint';
    const CONFIG_XML_PATH_API_VERSION = 'carriers/temando/api_version';
    const CONFIG_XML_PATH_ACCOUNT_ID = 'carriers/temando/account_id';
    const CONFIG_XML_PATH_BEARER_TOKEN = 'carriers/temando/bearer_token';

    const CONFIG_XML_PATH_BEARER_TOKEN_EXPIRY = 'carriers/temando/bearer_token_expiry';

    const CONFIG_XML_PATH_SYNC_ENABLED = 'carriers/temando/sync_enabled';
    const CONFIG_XML_PATH_SYNC_SHIPMENTS_ENABLED = 'carriers/temando/sync_shipments_enabled';
    const CONFIG_XML_PATH_SYNC_ORDERS_ENABLED = 'carriers/temando/sync_orders_enabled';
    const CONFIG_XML_PATH_SYNC_STREAM_ID = 'carriers/temando/sync_stream_id';

    const CONFIG_XML_PATH_CHECKOUT_FIELDS = 'carriers/temando/additional_checkout_fields';
    const CONFIG_XML_PATH_REGISTER_ACCOUNT_URL = 'carriers/temando/register_account_url';
    const CONFIG_XML_PATH_SHIPPING_PORTAL_URL = 'carriers/temando/shipping_portal_url';

    const CONFIG_XML_PATH_TEMANDO_RETURNS_ACTIVE = 'carriers/temando/rma_enabled';

    const CONFIG_XML_PATH_COLLECTION_POINTS_ENABLED = 'carriers/temando/collectionpoints_enabled';
    const CONFIG_XML_PATH_COLLECTION_POINTS_COUNTRIES = 'carriers/temando/collectionpoints_countries';

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ConfigAccessor
     */
    private $configAccessor;

    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * ModuleConfig constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     * @param ConfigAccessor $configAccessor
     * @param ModuleList $moduleList
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        ConfigAccessor $configAccessor,
        ModuleList $moduleList
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->configAccessor = $configAccessor;
        $this->moduleList = $moduleList;
    }

    /**
     * Check if shipping module is enabled in checkout.
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_CHECKOUT_ENABLED, $storeId);
    }

    /**
     * Check if webservice communication logging is enabled.
     *
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return (bool) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_LOGGING_ENABLED);
    }

    /**
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getStoreInformation($storeId = null)
    {
        $storeInformation = $this->configAccessor->getConfigValue(
            'general/store_information',
            $storeId
        );
        $storeInfo = $this->dataObjectFactory->create(['data' => (array)$storeInformation]);

        return $storeInfo;
    }

    /**
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getShippingOrigin($storeId = null)
    {
        $shippingOrigin = $this->configAccessor->getConfigValue(
            'shipping/origin',
            $storeId
        );
        $storeInfo = $this->dataObjectFactory->create(['data' => (array)$shippingOrigin]);

        return $storeInfo;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getWeightUnit($storeId = null)
    {
        return $this->configAccessor->getConfigValue('general/locale/weight_unit', $storeId);
    }

    /**
     * Obtain Register Account Url.
     *
     * @return string
     */
    public function getRegisterAccountUrl()
    {
        return (string) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_REGISTER_ACCOUNT_URL);
    }

    /**
     * Obtain Shipping Portal Url.
     *
     * @return string
     */
    public function getShippingPortalUrl()
    {
        return (string) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_SHIPPING_PORTAL_URL);
    }

    /**
     * Check if merchant registered an account at Temando.
     *
     * @return bool
     */
    public function isRegistered()
    {
        return ($this->getAccountId() !== '') && ($this->getBearerToken() !== '');
    }

    /**
     * Read URL of Temando REST API.
     *
     * @return string
     */
    public function getSessionEndpoint()
    {
        return $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_SESSION_ENDPOINT);
    }

    /**
     * Read URL of Temando REST API.
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_API_ENDPOINT);
    }

    /**
     * Save URL of Temando REST API.
     *
     * @param string $apiEndpoint
     * @return void
     */
    public function saveApiEndpoint($apiEndpoint)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_API_ENDPOINT, $apiEndpoint);
    }

    /**
     * Obtain the API version to connect to.
     *
     * @return string
     */
    public function getApiVersion()
    {
        return (string) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_API_VERSION);
    }

    /**
     * Read Temando Account Id.
     *
     * @return string
     */
    public function getAccountId()
    {
        return (string) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_ACCOUNT_ID);
    }

    /**
     * Save Temando Account Id.
     *
     * @param string $accountId
     * @return void
     */
    public function saveAccountId($accountId)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_ACCOUNT_ID, $accountId);
    }

    /**
     * Read Temando Authentication Token.
     *
     * @return string
     */
    public function getBearerToken()
    {
        return (string)$this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_BEARER_TOKEN);
    }

    /**
     * Save Temando Authentication Token.
     *
     * @param string $bearerToken
     * @return void
     */
    public function saveBearerToken($bearerToken)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_BEARER_TOKEN, $bearerToken);
    }

    /**
     * Read Temando Authentication Token Expiry Timestamp.
     *
     * @return string
     */
    public function getBearerTokenExpiry()
    {
        return (string) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_BEARER_TOKEN_EXPIRY);
    }

    /**
     * Save Temando Authentication Token Expiry Timestamp.
     *
     * @param string $bearerTokenExpiry
     * @return void
     */
    public function saveBearerTokenExpiry($bearerTokenExpiry)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_BEARER_TOKEN_EXPIRY, $bearerTokenExpiry);
    }

    /**
     * Check whether stream events should be processed or not.
     *
     * @return bool
     */
    public function isSyncEnabled()
    {
        return (bool) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_SYNC_ENABLED);
    }

    /**
     * Save new stream event processing configuration.
     *
     * @param string $value
     * @return void
     */
    public function saveSyncEnabled($value)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_SYNC_ENABLED, $value);
    }

    /**
     * Check whether shipment events should be processed or not.
     *
     * @return bool
     */
    public function isSyncShipmentEnabled()
    {
        return (bool) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_SYNC_SHIPMENTS_ENABLED);
    }

    /**
     * Save new stream event processing configuration.
     *
     * @param string $value
     * @return void
     */
    public function saveSyncShipmentEnabled($value)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_SYNC_SHIPMENTS_ENABLED, $value);
    }

    /**
     * Check whether order events should be processed or not.
     *
     * @return bool
     */
    public function isSyncOrderEnabled()
    {
        return (bool) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_SYNC_ORDERS_ENABLED);
    }

    /**
     * Save new stream event processing configuration.
     *
     * @param string $value
     * @return void
     */
    public function saveSyncOrderEnabled($value)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_SYNC_ORDERS_ENABLED, $value);
    }

    /**
     * Multiple streams may exists. Obtain the stream ID to request events from.
     *
     * @return string
     */
    public function getStreamId()
    {
        return (string) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_SYNC_STREAM_ID);
    }

    /**
     * Get checkout field definitions. This is the plain serialized string as stored in config.
     *
     * @return string
     */
    public function getCheckoutFieldsDefinition()
    {
        $checkoutFields = (string) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_CHECKOUT_FIELDS);
        if (!$checkoutFields) {
            $checkoutFields = '[]';
        }

        return $checkoutFields;
    }

    /**
     * Check if RMA feature is enabled.
     *
     * @return bool
     */
    public function isRmaEnabled()
    {
        return (bool) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_TEMANDO_RETURNS_ACTIVE);
    }

    /**
     * Check if RMA module is installed
     *
     * @return bool
     */
    public function isRmaAvailable()
    {
        return (bool) $this->moduleList->has('Magento_Rma');
    }

    /**
     * Save checkout field definitions in config.
     *
     * @param string $fieldsDefinition
     * @return void
     */
    public function saveCheckoutFieldsDefinition($fieldsDefinition)
    {
        $this->configAccessor->saveConfigValue(self::CONFIG_XML_PATH_CHECKOUT_FIELDS, $fieldsDefinition);
    }

    /**
     * Check if collection points feature is enabled in config.
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isCollectionPointsEnabled($storeId = null)
    {
        return (bool) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_COLLECTION_POINTS_ENABLED, $storeId);
    }

    /**
     * Obtain country codes enabled for collection point deliveries.
     *
     * @return string[]
     */
    public function getCollectionPointDeliveryCountries()
    {
        return (array) $this->configAccessor->getConfigValue(self::CONFIG_XML_PATH_COLLECTION_POINTS_COUNTRIES);
    }

    /**
     * Save new account data.
     *
     * @param string $accountId
     * @param string $bearerToken
     * @param string $bearerTokenExpiry
     * @return void
     */
    public function setAccount($accountId, $bearerToken, $bearerTokenExpiry)
    {
        $this->saveAccountId($accountId);
        $this->saveBearerToken($bearerToken);
        $this->saveBearerTokenExpiry($bearerTokenExpiry);
    }

    /**
     * Unset all account data.
     *
     * @return void
     */
    public function unsetAccount()
    {
        $this->configAccessor->deleteConfigValue(self::CONFIG_XML_PATH_ACCOUNT_ID);
        $this->configAccessor->deleteConfigValue(self::CONFIG_XML_PATH_BEARER_TOKEN);
        $this->configAccessor->deleteConfigValue(self::CONFIG_XML_PATH_BEARER_TOKEN_EXPIRY);
    }
}
