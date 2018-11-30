<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\PageAction;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\App\Response\RedirectInterface;

/**
 * Action Button to Order View or Pickup Listing Page
 *
 * @api
 * @package Temando\Shipping\Block
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 *
 */
class PickupBackButton extends Button
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * PickupBackButton constructor.
     * @param Context $context
     * @param RedirectInterface $redirect
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        RedirectInterface $redirect,
        array $data = []
    ) {
        $this->redirect = $redirect;

        parent::__construct($context, $data);
    }

    /**
     * Add button data
     *
     * @return Button
     */
    protected function _beforeToHtml()
    {
        $backUrl = $this->redirect->getRefererUrl();
        $this->setData('label', __('Back'));
        $this->setData('class', 'back');
        $this->setData('id', 'back');
        $this->setData('onclick', sprintf("setLocation('%s')", $backUrl));

        return parent::_beforeToHtml();
    }
}
