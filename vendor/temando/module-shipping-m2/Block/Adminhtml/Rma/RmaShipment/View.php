<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Rma\RmaShipment;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;

/**
 * RMA Shipment View
 *
 * @package  Temando\Shipping\Block
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class View extends Container
{
    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * View constructor.
     * @param Context $context
     * @param RmaAccess $rmaAccess
     * @param mixed[] $data
     */
    public function __construct(Context $context, RmaAccess $rmaAccess, array $data = [])
    {
        $this->rmaAccess = $rmaAccess;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Rma\Api\Data\RmaInterface|null
     */
    public function getRma()
    {
        return $this->rmaAccess->getCurrentRma();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Container
     */
    protected function _prepareLayout()
    {
        $rma = $this->rmaAccess->getCurrentRma();
        if (!$rma) {
            return parent::_prepareLayout();
        }

        $rmaEditUrl =  $this->getUrl('adminhtml/rma/edit', ['id' => $rma->getEntityId()]);
        $dispatchCreateUrl = $this->getUrl('temando/rma_shipment/dispatch', [
            'rma_id' => $rma->getEntityId(),
            'ext_shipment_id' => $this->rmaAccess->getCurrentRmaShipment()->getShipmentId()
        ]);

        $this->buttonList->remove('back');
        $this->buttonList->remove('save');

        $this->buttonList->add('back', [
            'label' => __('Back'),
            'class' => 'back',
            'onclick' => sprintf("setLocation('%s')", $rmaEditUrl)
        ]);
        $this->buttonList->add('temando_dispatch_return_shipment', [
            'label' => __('Dispatch Shipment'),
            'class' => 'primary',
            'onclick' => sprintf("setLocation('%s')", $dispatchCreateUrl)
        ]);

        return parent::_prepareLayout();
    }
}
