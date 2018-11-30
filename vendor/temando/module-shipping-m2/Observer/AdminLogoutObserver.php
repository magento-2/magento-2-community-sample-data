<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Temando\Shipping\Rest\AuthenticationInterface;

/**
 * Temando Logout Observer
 *
 * @package Temando\Shipping\Observer
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AdminLogoutObserver implements ObserverInterface
{
    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * AdminLogoutObserver constructor.
     * @param AuthenticationInterface $auth
     */
    public function __construct(AuthenticationInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->auth->disconnect();
    }
}
