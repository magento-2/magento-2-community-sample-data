<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Account;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * View model for account support details.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Support implements ArgumentInterface
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * Support constructor.
     * @param ModuleConfigInterface $moduleConfig
     */
    public function __construct(ModuleConfigInterface $moduleConfig)
    {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @return string
     */
    public function getShippingPortalUrl()
    {
        return $this->moduleConfig->getShippingPortalUrl();
    }
}
