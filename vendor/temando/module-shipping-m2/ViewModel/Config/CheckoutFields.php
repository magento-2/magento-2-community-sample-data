<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Config;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * View model for checkout fields JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CheckoutFields implements ArgumentInterface, PageActionsInterface, ShippingApiInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ShippingApiAccess
     */
    private $shippingApiAccess;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * CheckoutFields constructor.
     * @param RequestInterface $request
     * @param ShippingApiAccess $shippingApiAccess
     * @param UrlInterface $urlBuilder
     * @param ModuleConfigInterface $moduleConfig
     */
    public function __construct(
        RequestInterface $request,
        ShippingApiAccess $shippingApiAccess,
        UrlInterface $urlBuilder,
        ModuleConfigInterface $moduleConfig
    ) {
        $this->request = $request;
        $this->shippingApiAccess = $shippingApiAccess;
        $this->urlBuilder = $urlBuilder;
        $this->moduleConfig = $moduleConfig;
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
            'onclick' => sprintf("window.location.href = '%s';", $this->getConfigurationPageUrl()),
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
        return $this->shippingApiAccess;
    }

    /**
     * @return string
     */
    public function getUpdateCheckoutFieldEndpoint(): string
    {
        return $this->urlBuilder->getUrl('temando/settings_checkout/save');
    }

    /**
     * @return string
     */
    public function getConfigurationPageUrl(): string
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit', [
            'section' => 'carriers',
            '_fragment' => 'carriers_temando-link',
        ]);
    }

    /**
     * @return string
     */
    public function getCheckoutFieldsData(): string
    {
        return $this->moduleConfig->getCheckoutFieldsDefinition();
    }
}
