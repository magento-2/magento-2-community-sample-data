<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Plugin\Shipping;

use Magento\Backend\Block\Widget\Container;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Shipping\Block\Adminhtml\View as ShipmentView;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * AddShipmentViewToolbarButtonPlugin
 *
 * @package  Temando\Shipping\Plugin
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AddShipmentViewToolbarButtonPlugin
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * AddShipmentViewToolbarButtonPlugin constructor.
     *
     * @param ModuleConfigInterface $config
     * @param ShipmentProviderInterface $shipmentProvider
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ModuleConfigInterface $config,
        ShipmentProviderInterface $shipmentProvider,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->shipmentProvider = $shipmentProvider;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param ShipmentView|Container $block
     * @param LayoutInterface $layout
     *
     * @return null
     */
    public function beforeSetLayout(ShipmentView $block, LayoutInterface $layout)
    {
        // only display button if an API shipment was registered
        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            return null;
        }

        // only display button if rma is enabled for temando shipping
        if (!$this->config->isRmaEnabled()) {
            return null;
        }

        $salesShipment = $this->shipmentProvider->getSalesShipment();
        $shipmentId = $salesShipment->getEntityId();
        $extReturnShipmentId = $salesShipment->getExtensionAttributes()->getExtReturnShipmentId();

        // only display button if forward-fulfillment return shipment was booked
        if (!$extReturnShipmentId) {
            return null;
        }

        $viewUrl = $this->urlBuilder->getUrl(
            'temando/rma_shipment/view',
            ['shipment_id' => $shipmentId, 'ext_shipment_id' => $extReturnShipmentId]
        );

        $block->addButton(
            'view_return_shipment',
            [
                'label' => __('View Return Shipment'),
                'onclick' => sprintf("setLocation('%s')", $viewUrl)
            ],
            0,
            20
        );

        // original method's argument does not get changed.
        return null;
    }
}
