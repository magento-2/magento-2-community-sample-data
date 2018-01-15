<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\Shipping\View;

use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Backend\Block\Template\Context;
use Temando\Shipping\Model\Shipment\PackageCollection;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Package Listing Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class Package extends BackendTemplate
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
     * Set documentation from dispatch or shipment to block
     *
     * @return BackendTemplate
     */
    protected function _beforeToHtml()
    {
        if (!$this->hasData('packages')) {
            if ($this->shipmentProvider->getShipment()) {
                /** @var ShipmentInterface $platformShipment */
                $platformShipment = $this->shipmentProvider->getShipment();
                $this->setData('packages', $platformShipment->getPackages());
            }
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return PackageCollection|PackageInterface[]
     */
    public function getPackages()
    {
        return $this->hasData('packages') ? $this->getData('packages') : [];
    }
}
