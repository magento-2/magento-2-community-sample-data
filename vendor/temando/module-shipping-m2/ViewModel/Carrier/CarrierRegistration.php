<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Carrier;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\DataProvider\CarrierUrl;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;

/**
 * View model for carrier registration JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierRegistration implements ArgumentInterface, ShippingApiInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ShippingApiAccess
     */
    private $apiAccess;

    /**
     * @var CarrierUrl
     */
    private $carrierUrl;

    /**
     * CarrierRegistration constructor.
     * @param RequestInterface $request
     * @param ShippingApiAccess $apiAccess
     * @param CarrierUrl $carrierUrl
     */
    public function __construct(
        RequestInterface $request,
        ShippingApiAccess $apiAccess,
        CarrierUrl $carrierUrl
    ) {
        $this->request = $request;
        $this->apiAccess = $apiAccess;
        $this->carrierUrl = $carrierUrl;
    }

    /**
     * @return ShippingApiAccessInterface
     */
    public function getShippingApiAccess(): ShippingApiAccessInterface
    {
        return $this->apiAccess;
    }

    /**
     * @return EntityUrlInterface|CarrierUrl
     */
    public function getCarrierUrl(): EntityUrlInterface
    {
        return $this->carrierUrl;
    }

    /**
     * Obtain the Temando carrier integration id.
     *
     * @return string The Temando carrier integration ID.
     */
    public function getCarrierIntegrationId(): string
    {
        $integrationId = $this->request->getParam(CarrierInterface::INTEGRATION_ID);
        return preg_replace('/[^\w0-9-_]/', '', $integrationId);
    }
}
