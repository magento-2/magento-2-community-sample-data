<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Configuration;

use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\Block\Adminhtml\Template\AbstractComponent;

/**
 * Temando Locations Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.0.5 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Location\LocationEdit
 */
class LocationComponent extends AbstractComponent
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
            'onclick' => sprintf("window.location.href = '%s';", $this->getLocationsPageUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];

        $this->buttonList->add('back', $buttonData);

        return parent::_prepareLayout();
    }

    /**
     * Obtain locations grid url
     *
     * @return string
     */
    public function getLocationsPageUrl()
    {
        return $this->getUrl('temando/configuration_location/index');
    }

    /**
     * Obtain Add New Carrier url.
     *
     * @return string
     */
    public function getNewLocationPageUrl()
    {
        return $this->getUrl('temando/configuration_location/new');
    }

    /**
     * Obtain the Temando location id that is passed from grid to edit component.
     * Think of it as a GUID rather than a location id in the local storage.
     *
     * @return string The Temando location id
     */
    public function getLocationId()
    {
        $locationId = $this->getRequest()->getParam(LocationInterface::LOCATION_ID);
        return preg_replace('/[^\w0-9-_]/', '', $locationId);
    }
}
