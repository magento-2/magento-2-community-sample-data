<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\PackageInfo;

/**
 * Temando Config Version Info Block
 *
 * @package Temando\Shipping\Block
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 *
 * @api
 */
class Version extends Field
{
    /**
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * Version constructor.
     *
     * @param Context $context
     * @param PackageInfo $packageInfo
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        PackageInfo $packageInfo,
        array $data = []
    ) {
        $this->packageInfo = $packageInfo;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('text', $this->packageInfo->getVersion('Temando_Shipping'));
        return parent::_getElementHtml($element);
    }
}
