<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ConfigurationValidator;

/**
 * Displays the result of a validation against the Vertex configuration settings
 */
class VertexStatus extends Field
{
    /** @var Config */
    private $config;

    /** @var ConfigurationValidator */
    private $configurationValidator;

    /**
     * @param Context $context
     * @param Config $config
     * @param ConfigurationValidator $configurationValidator
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        ConfigurationValidator $configurationValidator,
        array $data = []
    ) {
        $this->config = $config;
        $this->configurationValidator = $configurationValidator;

        parent::__construct($context, $data);
    }

    /**
     * Get markup showing status and/or validity of Vertex configuration
     *
     * MEQP2 Warning: Protected method. Required to override Field's _getElementHtml
     * MEQP2 Warning: Discouraged function. Required for PHP < 5.6 compatibility
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Necessary for override
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $store = (int) $this->getRequest()->getParam('store', 0);
        $website = (int) $this->getRequest()->getParam('website', 0);
        $scopeId = $website > 0 ? $website : $store;
        $useWebsite = $website > 0;
        $scopeType = $useWebsite ? ScopeInterface::SCOPE_WEBSITE : ScopeInterface::SCOPE_STORE;

        if (!$this->config->isVertexActive($scopeId, $scopeType)) {
            $state = 'critical';
            $status = 'Disabled';
        } else {
            $result = $this->configurationValidator->execute($scopeType, $scopeId);
            if ($result->isValid()) {
                $state = 'notice';
                $status = 'Valid';
            } else {
                $message = $result->getMessage();
                $arguments = $result->getArguments();

                $state = 'minor';
                $status = call_user_func_array('__', array_merge([$message], $arguments));
            }
        }

        return '<span class="grid-severity-' . $state . '"><span>' . $status . '</span></span>';
    }

    /**
     * Determine if the "Use default value" inheritance checkbox should be shown.
     *
     * Implementation: No.
     *
     * @param AbstractElement $element
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _isInheritCheckboxRequired(AbstractElement $element)
    {
        return false;
    }
}
