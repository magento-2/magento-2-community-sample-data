<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\Shipping\View;

use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Backend\Block\Template\Context;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Package Listing Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.1.3 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Shipment\ShipmentDetails
 */
class Package extends BackendTemplate
{
    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @param Context                   $context
     * @param ShipmentProviderInterface $shipmentProvider
     * @param RmaAccess                 $rmaAccess
     * @param mixed[]                   $data
     */
    public function __construct(
        Context $context,
        ShipmentProviderInterface $shipmentProvider,
        RmaAccess $rmaAccess,
        array $data = []
    ) {
        $this->shipmentProvider = $shipmentProvider;
        $this->rmaAccess = $rmaAccess;

        parent::__construct($context, $data);
    }

    /**
     * Set package from dispatch or shipment to block
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
            } elseif ($this->rmaAccess->getCurrentRmaShipment()) {
                /** @var ShipmentInterface $platformShipment */
                $platformShipment = $this->rmaAccess->getCurrentRmaShipment();
                $this->setData('packages', $platformShipment->getPackages());
            }
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return PackageInterface[]
     */
    public function getPackages()
    {
        return $this->hasData('packages') ? $this->getData('packages') : [];
    }
}
