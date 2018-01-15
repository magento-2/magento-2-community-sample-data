<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Carrier Interface.
 *
 * The carrier data object represents one item in the carriers grid listing.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CarrierInterface
{
    const CONFIGURATION_ID = 'configuration_id';
    const INTEGRATION_ID = 'integration_id';
    const NAME = 'name';
    const CONNECTION_NAME = 'connection_name';
    const STATUS = 'status';
    const ACTIVE_SERVICES = 'active_services';
    const LOGO = 'logo';

    /**
     * @return string
     */
    public function getConfigurationId();

    /**
     * @return string
     */
    public function getIntegrationId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getConnectionName();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string[]
     */
    public function getActiveServices();

    /**
     * @return string
     */
    public function getLogo();
}
