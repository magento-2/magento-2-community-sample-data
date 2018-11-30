<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Configuration;

use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\Block\Adminhtml\Template\AbstractComponent;

/**
 * Temando Carrier Component Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.0.5 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Carrier\CarrierEdit
 * @see \Temando\Shipping\ViewModel\Carrier\CarrierRegistration
 */
class CarrierComponent extends AbstractComponent
{
    /**
     * Add Back Button.
     *
     * @return \Magento\Backend\Block\Widget\Container
     */
    protected function _prepareLayout()
    {
        $buttonData = [
            'label' => __('Back'),
            'onclick' => sprintf("window.location.href = '%s';", $this->getCarriersPageUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];

        $this->buttonList->add('back', $buttonData);

        return parent::_prepareLayout();
    }
    /**
     * Obtain carriers grid url.
     *
     * @return string
     */
    public function getCarriersPageUrl()
    {
        return $this->getUrl('temando/configuration_carrier/index');
    }

    /**
     * Obtain Add New Carrier url.
     *
     * @return string
     */
    public function getCarrierRegistrationPageUrl()
    {
        return $this->getUrl('temando/configuration_carrier/register');
    }

    /**
     * Obtain Add New Carrier url.
     *
     * @return string
     */
    public function getAvailableCarriersPageUrl()
    {
        return $this->getUrl('temando/configuration_carrier/new');
    }

    /**
     * Obtain the Temando carrier id that is passed from init to edit component.
     * Think of it as a GUID rather than a carrier id in the local storage.
     *
     * @return string The Temando carrier GUID.
     */
    public function getCarrierConfigurationId()
    {
        $configurationId = $this->getRequest()->getParam(CarrierInterface::CONFIGURATION_ID);
        return preg_replace('/[^\w0-9-_]/', '', $configurationId);
    }

    /**
     * Obtain the Temando carrier integration id.
     *
     * @return string The Temando carrier integration ID.
     */
    public function getCarrierIntegrationId()
    {
        $integrationId = $this->getRequest()->getParam(CarrierInterface::INTEGRATION_ID);
        return preg_replace('/[^\w0-9-_]/', '', $integrationId);
    }
}
