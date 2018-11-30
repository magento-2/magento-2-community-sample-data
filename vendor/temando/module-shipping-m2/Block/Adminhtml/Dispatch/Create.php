<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Dispatch;

use Temando\Shipping\Block\Adminhtml\Template\AbstractComponent;

/**
 * Temando Dispatch Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.0.5 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Dispatch\DispatchEdit
 */
class Create extends AbstractComponent
{
    /**
     * Obtain dispatches grid url
     *
     * @return string
     */
    public function getDispatchesPageUrl()
    {
        return $this->_urlBuilder->getUrl('temando/dispatch/index');
    }
}
