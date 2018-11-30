<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando Login Observer
 *
 * @package Temando\Shipping\Observer
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AdminLoginObserver implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Carrier
     */
    private $carrier;

    /**
     * @var WsConfigInterface
     */
    private $config;

    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * AdminLoginObserver constructor.
     *
     * @param ManagerInterface $messageManager
     * @param Carrier $carrier
     * @param WsConfigInterface $config
     * @param AuthenticationInterface $auth
     */
    public function __construct(
        ManagerInterface $messageManager,
        Carrier $carrier,
        WsConfigInterface $config,
        AuthenticationInterface $auth
    ) {
        $this->messageManager = $messageManager;
        $this->carrier = $carrier;
        $this->config = $config;
        $this->auth = $auth;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->carrier->getConfigFlag('active')) {
            return;
        }

        $bearerToken = $this->config->getBearerToken();
        $accountId = $this->config->getAccountId();

        try {
            $this->auth->connect($accountId, $bearerToken);
        } catch (InputException $e) {
            // credentials missing
            $msg = 'Temando Shipping is not properly configured. Please register an account.';
            $this->messageManager->addWarningMessage(__($msg));
        } catch (LocalizedException $e) {
            // other
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }
    }
}
