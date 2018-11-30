<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel;

/**
 * Page Main Actions Provider Interface
 *
 * All view models that provide a page actions menu bar must implement this.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface PageActionsInterface
{
    /**
     * Obtain array of button data.
     *
     * @see \Temando\Shipping\Block\Adminhtml\ComponentContainer::_prepareLayout
     * @see \Magento\Backend\Block\Widget\Button\ButtonList::add
     *
     * @return mixed[][]
     */
    public function getMainActions(): array;
}
