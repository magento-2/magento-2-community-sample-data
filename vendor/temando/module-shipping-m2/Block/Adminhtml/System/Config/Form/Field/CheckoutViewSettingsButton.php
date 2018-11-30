<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Checkout Fields settings button in module configuration
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class CheckoutViewSettingsButton extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('value', __("Configure"));
        $element->setData('class', "action-default");
        $element->setData('onclick', "window.location.assign('" . $this->getActionUrl() . "')");
        return parent::_getElementHtml($element);
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->_urlBuilder->getUrl('temando/settings_checkout/edit');
    }
}
