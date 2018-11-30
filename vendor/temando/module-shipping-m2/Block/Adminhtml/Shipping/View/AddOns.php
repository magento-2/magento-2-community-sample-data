<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Shipping\View;

use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Temando\Shipping\Model\Shipment\CapabilityInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando AddOn Listing Layout Block
 *
 * @deprecated since 1.2.0 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Shipment\AddOns
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class AddOns extends BackendTemplate
{
    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @param Context                   $context
     * @param ShipmentProviderInterface $shipmentProvider
     * @param mixed[]                   $data
     */
    public function __construct(
        Context $context,
        ShipmentProviderInterface $shipmentProvider,
        array $data = []
    ) {
        $this->shipmentProvider = $shipmentProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return DataObject[]
     */
    public function getCapabilities()
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        return ($shipment ? $shipment->getCapabilities() : []);
    }

    /**
     * @return string
     */
    public function getAddressType()
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        return ($shipment ? $shipment->getDestinationLocation()->getType() : '');
    }

    /**
     * @param DataObject $capability
     *
     * @return string
     */
    public function renderCapability(DataObject $capability)
    {
        /** @var BackendTemplate $addOnBlock */
        $addOnBlock = $this->getChildBlock('addon_item');
        $addOnBlock->setData('capability', $capability);

        $templates = $this->getData('templates');
        /** @var CapabilityInterface $capability */
        if (!isset($templates[$capability->getCapabilityId()])) {
            $addOnBlock->setTemplate($templates['default']);
        } else {
            $addOnBlock->setTemplate($templates[$capability->getCapabilityId()]);
        }

        return $addOnBlock->toHtml();
    }

    /**
     * @param string $addressType
     * @return string
     */
    public function renderAddressType($addressType)
    {
        /** @var BackendTemplate $addOnBlock */
        $addOnBlock = $this->getChildBlock('addon_item');
        $addOnBlock->setData('addressType', $addressType);
        $templates  = $this->getData('templates');
        $addOnBlock->setTemplate($templates['addressType']);

        return $addOnBlock->toHtml();
    }
}
