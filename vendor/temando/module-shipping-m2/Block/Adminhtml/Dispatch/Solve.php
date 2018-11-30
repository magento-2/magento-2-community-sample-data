<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Dispatch;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Container;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\ShipmentSearchResultInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\DispatchProviderInterface;

/**
 * Temando Dispatch Solve Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class Solve extends Container
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var DispatchProviderInterface
     */
    private $dispatchProvider;

    /**
     * Solve constructor.
     *
     * @param Context $context
     * @param ModuleConfigInterface $config
     * @param DispatchProviderInterface $dispatchProvider
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        ModuleConfigInterface $config,
        DispatchProviderInterface $dispatchProvider,
        array $data = []
    ) {
        $this->config = $config;
        $this->dispatchProvider = $dispatchProvider;

        parent::__construct($context, $data);
    }

    /**
     * Add Back Button.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $buttonData = [
            'label' => __('Back'),
            'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class' => 'back',
        ];

        $this->addButton('back', $buttonData, -1);

        return parent::_prepareLayout();
    }

    /**
     * @return \Temando\Shipping\Model\Dispatch\Shipment[]
     */
    public function getFailedShipments()
    {
        $dispatch = $this->dispatchProvider->getDispatch();
        if (!$dispatch) {
            return [];
        }

        return $dispatch->getFailedShipments();
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        $dispatch = $this->dispatchProvider->getDispatch();
        if (!$dispatch) {
            return $this->_urlBuilder->getUrl('temando/dispatch/index');
        }

        return $this->_urlBuilder->getUrl('temando/dispatch/view', [
            'dispatch_id' => $dispatch->getDispatchId()
        ]);
    }

    /**
     * @return string
     */
    public function getNewUrl()
    {
        return $this->_urlBuilder->getUrl('temando/dispatch/new');
    }

    /**
     * @param string $extShipmentId
     * @return string
     */
    public function getShipmentUrl($extShipmentId)
    {
        return $this->_urlBuilder->getUrl('temando/shipment/view', ['shipment_id' => $extShipmentId]);
    }

    /**
     * @return string
     */
    public function getShippingPortalUrl()
    {
        return $this->config->getShippingPortalUrl();
    }
}
