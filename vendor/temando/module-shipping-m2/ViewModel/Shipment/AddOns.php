<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Shipment;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Temando\Shipping\Model\Shipment\CapabilityInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * View model for shipment capabilities.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class AddOns implements ArgumentInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * AddOns constructor.
     * @param LayoutInterface $layout
     * @param ShipmentProviderInterface $shipmentProvider
     */
    public function __construct(
        LayoutInterface $layout,
        ShipmentProviderInterface $shipmentProvider
    ) {
        $this->layout = $layout;
        $this->shipmentProvider = $shipmentProvider;
    }

    /**
     * @return CapabilityInterface[]
     */
    private function getCapabilities()
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            return [];
        }

        return (array) $shipment->getCapabilities();
    }

    /**
     * @return string
     */
    private function getAddressType()
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        if (!$shipment) {
            return '';
        }

        return (string) $shipment->getDestinationLocation()->getType();
    }

    /**
     * @return string
     */
    public function getAddOnsHtml()
    {
        $addOnsHtml = '';

        $capabilities = $this->getCapabilities();
        $addressType = $this->getAddressType();

        if (empty($capabilities) && empty($addressType)) {
            return $addOnsHtml;
        }

        /** @var Template $addOnBlock */
        $addOnBlock = $this->layout->getBlock('addon_listing');
        $templates = $addOnBlock->getData('templates');

        $addOnBlock = $this->layout->createBlock(Template::class, 'addon_item');
        foreach ($capabilities as $capability) {
            $addOnBlock->setData('capability', $capability);

            if (!isset($templates[$capability->getCapabilityId()])) {
                $addOnBlock->setTemplate($templates['default']);
            } else {
                $addOnBlock->setTemplate($templates[$capability->getCapabilityId()]);
            }

            $addOnsHtml.= $addOnBlock->toHtml();
        }

        $addOnBlock->setData('address_type', $addressType);
        $addOnBlock->setTemplate($templates['addressType']);
        $addOnsHtml.= $addOnBlock->toHtml();

        return $addOnsHtml;
    }
}
