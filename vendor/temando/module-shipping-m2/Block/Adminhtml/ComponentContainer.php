<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;

/**
 * Default block for all pages that display Temando components
 *
 * @package  Temando\Shipping\Block
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class ComponentContainer extends Container
{
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @param Context $context
     * @param RemoteAddress $remoteAddress
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        RemoteAddress $remoteAddress,
        array $data = []
    ) {
        $this->remoteAddress = $remoteAddress;

        parent::__construct($context, $data);
    }

    /**
     * Obtain view model for display data.
     *
     * There is no getter in the core like it is offered for the jsLayout argument…
     *
     * @return ArgumentInterface|null
     */
    public function getViewModel()
    {
        return $this->getData('viewModel');
    }

    /**
     * Obtain componentry assets base url.
     *
     * @return string
     */
    public function getAssetsUrl()
    {
        return $this->getViewFileUrl('Temando_Shipping') . '/';
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        $localeCode = $this->_scopeConfig->getValue(
            DirectoryHelper::XML_PATH_DEFAULT_LOCALE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return strtolower(str_replace('_', '-', $localeCode));
    }

    /**
     * Obtain Language Code.
     *
     * @return string
     */
    public function getLanguage()
    {
        return substr_replace($this->getLocale(), '', 2);
    }

    /**
     * Obtain merchant IP address.
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * Add Action Buttons.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $viewModel = $this->getViewModel();
        if (!$viewModel instanceof PageActionsInterface) {
            return parent::_prepareLayout();
        }

        $actions = $viewModel->getMainActions();
        if (!empty($actions) && is_array($actions)) {
            array_walk($actions, function ($buttonData, $buttonId) {
                $this->buttonList->add($buttonId, $buttonData);
            });
        }

        return parent::_prepareLayout();
    }
}
