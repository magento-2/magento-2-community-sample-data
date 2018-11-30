<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Dispatch;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\ViewModel\DataProvider\DispatchUrl;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * View model for dispatch new/edit JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchEdit implements ArgumentInterface, ShippingApiInterface
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
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * DispatchEdit constructor.
     * @param ShippingApiAccess $apiAccess
     * @param DispatchUrl $dispatchUrl
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ShippingApiAccess $apiAccess,
        DispatchUrl $dispatchUrl,
        UrlInterface $urlBuilder
    ) {
        $this->apiAccess = $apiAccess;
        $this->dispatchUrl = $dispatchUrl;
        $this->urlBuilder = $urlBuilder;
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

    /**
     * @return string
     */
    public function getShipmentViewPageUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/shipment/view', ['shipment_id' => '--id--']);
    }
}
