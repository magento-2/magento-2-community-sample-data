<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Rma Block class for rendering the configuration
 *
 * @package  Temando\Shipping\Block
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class Rma extends Field
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * Rma constructor.
     *
     * @param Context               $context
     * @param ModuleConfigInterface $moduleConfig
     * @param array                 $data
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($this->moduleConfig->isRmaAvailable()) {
            return parent::render($element);
        }

        return '';
    }
}
