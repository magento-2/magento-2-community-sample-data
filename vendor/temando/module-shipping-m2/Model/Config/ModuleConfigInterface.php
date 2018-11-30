<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Config;

/**
 * Temando Config Interface
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ModuleConfigInterface
{
    /**
     * Check if shipping module is enabled in checkout.
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null);

    /**
     * Check if merchant registered an account at Temando.
     *
     * @return bool
     */
    public function isRegistered();

    /**
     * Obtain store information settings.
     *
     * @param int $storeId
     *
     * @return \Magento\Framework\DataObject
     */
    public function getStoreInformation($storeId = null);

    /**
     * Obtain shipping origin settings.
     *
     * @param int $storeId
     *
     * @return \Magento\Framework\DataObject
     */
    public function getShippingOrigin($storeId = null);

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function getWeightUnit($storeId = null);

    /**
     * Obtain Register Account Url.
     *
     * @return string
     */
    public function getRegisterAccountUrl();

    /**
     * Obtain Shipping Portal Url.
     *
     * @return string
     */
    public function getShippingPortalUrl();

    /**
     * Check whether stream events as whole should be processed or not.
     *
     * @return bool
     */
    public function isSyncEnabled();

    /**
     * Save new stream event processing configuration.
     *
     * @param string $value
     */
    public function saveSyncEnabled($value);

    /**
     * Check whether shipment events should be processed or not.
     *
     * @return bool
     */
    public function isSyncShipmentEnabled();

    /**
     * Save new stream event processing configuration.
     *
     * @param string $value
     */
    public function saveSyncShipmentEnabled($value);

    /**
     * Check whether order events should be processed or not.
     *
     * @return bool
     */
    public function isSyncOrderEnabled();

    /**
     * Save new stream event processing configuration.
     *
     * @param string $value
     */
    public function saveSyncOrderEnabled($value);

    /**
     * Multiple streams may exists. Obtain the stream ID to request events from.
     *
     * @return string
     */
    public function getStreamId();

    /**
     * Obtain checkout fields definition.
     *
     * @return string
     */
    public function getCheckoutFieldsDefinition();

    /**
     * Save checkout fields definition.
     *
     * @param string $fieldsDefinition
     * @return void
     */
    public function saveCheckoutFieldsDefinition($fieldsDefinition);

    /**
     * Check if RMA feature is enabled.
     *
     * @return bool
     */
    public function isRmaEnabled();

    /**
     * Check if RMA module is installed.
     *
     * @return bool
     */
    public function isRmaAvailable();

    /**
     * Check if collection points feature is enabled in config.
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isCollectionPointsEnabled($storeId = null);

    /**
     * Obtain country codes enabled for collection point deliveries.
     *
     * @param int $storeId
     *
     * @return string[]
     */
    public function getCollectionPointDeliveryCountries($storeId = null);

    /**
     * Check if click and collect feature is enabled in config.
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isClickAndCollectEnabled($storeId = null);
}
