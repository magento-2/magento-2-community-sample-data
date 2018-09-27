<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\EventStream;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Class Edit
 *
 * @package  Temando\Shipping\Block
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class Edit extends Container
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param ModuleConfigInterface $config
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        ModuleConfigInterface $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Add action buttons.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $buttonData = [
            'label' => __('Back'),
            'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class' => 'back'
        ];

        $this->addButton('back', $buttonData, -1);

        $buttonData = [
            'label' => __('Save'),
            'class' => 'save primary',
            'onclick' => 'document.getElementById("sync_form").submit();',
        ];

        $this->addButton('save', $buttonData, -1);

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit', [
            'section' => 'carriers',
            '_fragment' => 'carriers_temando-link',
        ]);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl('*/*/save');
    }

    /**
     * @return mixed[][]
     */
    public function getInputs()
    {
        $fields = [
            [
                'heading' => __('Entities to sync'),
                'label' => __('Shipment'),
                'name' => 'sync_shipment',
                'id' => 'sync_shipment',
                'checked' => $this->config->isSyncShipmentEnabled(),
                'disabled' => ''
            ],
        ];

        return $fields;
    }

    /**
     * @return mixed[]
     */
    public function getInputEnable()
    {
        return [
            'label' => __('Enable sync'),
            'name' => 'sync_enable',
            'id' => 'sync_enable',
            'checked' => $this->config->isSyncEnabled()
        ];
    }
}
