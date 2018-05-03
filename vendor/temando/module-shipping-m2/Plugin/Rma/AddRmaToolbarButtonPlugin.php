<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Plugin\Rma;

use Magento\Backend\Block\Widget\Container;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Rma\Block\Adminhtml\Rma\Edit as RmaEdit;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * AddRmaToolbarButtonPlugin
 *
 * @package  Temando\Shipping\Plugin
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AddRmaToolbarButtonPlugin
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * AddRmaToolbarButtonPlugin constructor.
     * @param ModuleConfigInterface $config
     * @param RmaAccess $rmaAccess
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ModuleConfigInterface $config,
        RmaAccess $rmaAccess,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->rmaAccess = $rmaAccess;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param RmaEdit|Container $block
     * @param LayoutInterface $block
     *
     * @return null
     */
    public function beforeSetLayout(RmaEdit $block, LayoutInterface $layout)
    {
        // only display button if rma is enabled for temando shipping
        if (!$this->config->isRmaEnabled()) {
            return null;
        }

        // only display button if rma is registered
        /** @var \Magento\Rma\Model\Rma $rma */
        $rma = $this->rmaAccess->getCurrentRma();
        if (!$rma) {
            return null;
        }

        // only display button if original order was shipped with temando shipping
        $shippingMethod = $rma->getOrder()->getShippingMethod(true);
        $carrierCode = $shippingMethod->getData('carrier_code');
        if ($carrierCode !== Carrier::CODE) {
            return null;
        }

        $isRmaAuthorized = in_array($rma->getStatus(), [
            \Magento\Rma\Model\Rma\Source\Status::STATE_AUTHORIZED,
            \Magento\Rma\Model\Rma\Source\Status::STATE_PARTIAL_AUTHORIZED
        ]);
        $createUrl = $this->urlBuilder->getUrl(
            'temando/rma_shipment/create',
            ['rma_id' => $rma->getId()]
        );

        $block->addButton(
            'create_return_shipment',
            [
                'label' => __('Create Return Shipment'),
                'class' => $isRmaAuthorized ? '' : 'disabled',
                'onclick' => sprintf("setLocation('%s')", $createUrl)
            ],
            6
        );

        // original method's argument does not get changed.
        return null;
    }
}
