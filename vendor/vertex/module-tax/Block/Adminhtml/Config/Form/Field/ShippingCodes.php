<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\ScopeInterface;

/**
 * Renders a list of shipping codes to be used as a reference when setting up Vertex
 */
class ShippingCodes extends Field
{
    /** @var Config */
    private $shippingConfig;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @param Context $context
     * @param Config $shippingConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $shippingConfig,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->shippingConfig = $shippingConfig;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<table cellspacing="0" class="data-grid"><thead>';
        $html .= '<tr><th class="data-grid-th">Shipping Method</th><th class="data-grid-th">Product Code</th></tr>';
        $html .= '</thead><tbody>';
        $allowedMethods = ['ups', 'usps', 'fedex', 'dhl'];
        $methods = $this->shippingConfig->getActiveCarriers();

        foreach ($methods as $carrierCode => $carrier) {
            $methodOptions = [];

            if ($carrierMethods = $carrier->getAllowedMethods()) {
                $title = $this->scopeConfig->getValue(
                    "carriers/$carrierCode/title",
                    ScopeInterface::SCOPE_STORE
                );

                if (!$title) {
                    $title = $carrierCode;
                }

                $html .= '<tr><th class="data-grid-th"   colspan="2">' . $this->escapeHtml($title) . '</th></tr>';

                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $methodOptions[] = [
                        'value' => $code,
                        'label' => $method
                    ];
                }

                $html .= '<tr class="" >';
                $html .= '<td class="label"  style="padding:1rem;" >' . $this->escapeHtml($method) . ': </td>';
                $html .= '<td class="value" style="padding:1rem;" > ' . $this->escapeHtml($code) . '</td>';
                $html .= '</tr>';
            }

            if (in_array($carrierCode, $allowedMethods)) {
                foreach ($carrierMethods as $k => $v) {
                    $html .= '<tr>';
                    $html .= '<td class="label"  style="padding:1rem;">' . $this->escapeHtml($v) . ': </td>';
                    $html .= '<td class="value" style="padding:1rem;" > ';
                    $html .= $this->escapeHtml($carrierCode . '_' . $k) . '</td>';
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Magento\Config\Block\System\Config\Form\Field::render()
     */
    public function render(AbstractElement $element)
    {
        $html = '<td>';
        $html .= $this->getElementHtml();
        $html .= '</td>';

        return $this->_decorateRowHtml($element, $html);
    }
}
