<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Temando Config Getting Started Info Block
 *
 * @package  Temando\Shipping\Block
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class GettingStarted extends Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/form/field/getting_started.phtml';

    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * GettingStarted constructor.
     *
     * @param Context $context
     * @param ModuleConfigInterface $moduleConfig
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        ModuleConfigInterface $moduleConfig,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = sprintf(
            '<td colspan="%d" id="%s">%s</td>',
            3 + (int)$this->_isInheritCheckboxRequired($element),
            $element->getHtmlId(),
            $this->_renderValue($element)
        );

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Render element value
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return bool
     */
    public function isMerchantRegistered()
    {
        return $this->moduleConfig->isRegistered();
    }

    /**
     * @return string
     */
    public function getLocationsUrl()
    {
        return $this->_urlBuilder->getUrl('temando/configuration_location/index');
    }

    /**
     * @return string
     */
    public function getCarriersUrl()
    {
        return $this->_urlBuilder->getUrl('temando/configuration_carrier/index');
    }

    /**
     * @return string
     */
    public function getPackagesUrl()
    {
        return $this->_urlBuilder->getUrl('temando/configuration_packaging/index');
    }

    /**
     * @return string
     */
    public function getShippingPortalUrl()
    {
        return $this->moduleConfig->getShippingPortalUrl();
    }
}
