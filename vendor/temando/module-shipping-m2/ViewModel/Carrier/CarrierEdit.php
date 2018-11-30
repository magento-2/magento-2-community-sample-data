<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Carrier;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\DataProvider\CarrierUrl;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;

/**
 * View model for carrier new/edit JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierEdit implements ArgumentInterface, PageActionsInterface, ShippingApiInterface
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
     * CarrierEdit constructor.
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
     * Obtain array of button data.
     *
     * @see \Temando\Shipping\Block\Adminhtml\ComponentContainer::_prepareLayout
     * @see \Magento\Backend\Block\Widget\Button\ButtonList::add
     *
     * @return mixed[][]
     */
    public function getMainActions(): array
    {
        $buttonId = 'back';
        $buttonData = [
            'label' => __('Back'),
            'onclick' => sprintf("window.location.href = '%s';", $this->carrierUrl->getListActionUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];

        $mainActions = [
            $buttonId => $buttonData,
        ];

        return $mainActions;
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
     * Obtain the Temando carrier id that is passed from init to edit component.
     * Think of it as a GUID rather than a carrier id in the local storage.
     *
     * @return string The Temando carrier GUID.
     */
    public function getCarrierConfigurationId(): string
    {
        $configurationId = $this->request->getParam(CarrierInterface::CONFIGURATION_ID);
        return preg_replace('/[^\w0-9-_]/', '', $configurationId);
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
