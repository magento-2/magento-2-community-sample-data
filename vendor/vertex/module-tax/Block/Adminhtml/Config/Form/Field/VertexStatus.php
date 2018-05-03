<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CredentialChecker;

/**
 * Displays the result of a validation against the Vertex configuration settings
 */
class VertexStatus extends Field
{
    /** @var Config */
    private $config;

    /** @var CredentialChecker */
    private $credentialChecker;

    /**
     * @param Context $context
     * @param Config $config
     * @param CredentialChecker $credentialChecker
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        CredentialChecker $credentialChecker,
        array $data = []
    ) {
        $this->config = $config;
        $this->credentialChecker = $credentialChecker;

        parent::__construct($context, $data);
    }

    /**
     * Get markup showing status and/or validity of Vertex configuration
     *
     * MEQP2 Warning: Protected method.  Required to override Field's _getElementHtml
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Necessary for override
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $store = (int)$this->getRequest()->getParam('store', 0);

        if (!$this->config->isVertexActive($store)) {
            $state = 'critical';
            $status = 'Disabled';
        } else {
            $result = $this->credentialChecker->validate($store);
            if ($result->isValid()) {
                $state = 'notice';
                $status = 'Valid';
            } else {
                $message = $result->getMessage();
                $arguments = $result->getArguments();

                $state = 'minor';
                $status = __($message, ...$arguments);
            }
        }

        return '<span class="grid-severity-' . $state . '"><span>' . $status . '</span></span>';
    }
}
