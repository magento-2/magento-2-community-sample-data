<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Dispatch;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\DataProvider\DispatchUrl;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;

/**
 * View model for dispatch list JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchListing implements ArgumentInterface, ShippingApiInterface
{
    /**
     * @var ShippingApiAccess
     */
    private $apiAccess;

    /**
     * @var DispatchUrl
     */
    private $dispatchUrl;

    /**
     * DispatchListing constructor.
     * @param ShippingApiAccess $apiAccess
     * @param DispatchUrl $dispatchUrl
     */
    public function __construct(
        ShippingApiAccess $apiAccess,
        DispatchUrl $dispatchUrl
    ) {
        $this->apiAccess = $apiAccess;
        $this->dispatchUrl = $dispatchUrl;
    }

    /**
     * @return ShippingApiAccessInterface
     */
    public function getShippingApiAccess(): ShippingApiAccessInterface
    {
        return $this->apiAccess;
    }

    /**
     * @return EntityUrlInterface|DispatchUrl
     */
    public function getDispatchUrl(): EntityUrlInterface
    {
        return $this->dispatchUrl;
    }
}
