<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * MerchantRegisteredFlagObserver Observer
 *
 * @package  Temando\Shipping\Observer
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class MerchantRegisteredFlagObserver implements ObserverInterface
{
    /**
     * @var WsConfigInterface
     */
    private $config;

    /**
     * MerchantRegisteredFlagObserver constructor.
     * @param WsConfigInterface $config
     */
    public function __construct(WsConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Check if account id and bearer token where entered in backend config.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $accountId   = $this->config->getAccountId();
        $bearerToken = $this->config->getBearerToken();
        $bearerTokenExpiry = $this->config->getBearerTokenExpiry();

        if (!$accountId && !$bearerToken) {
            // set "merchant needs activation" flag
            $this->config->unsetAccount();
        } elseif ($accountId && $bearerToken) {
            // unset "merchant needs activation" flag
            $this->config->setAccount($accountId, $bearerToken, $bearerTokenExpiry);
        }
    }
}
