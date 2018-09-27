<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma\RmaShipment;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment\PackageInterface;

/**
 * View model for RMA shipment items.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Items implements ArgumentInterface
{
    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * Items constructor.
     * @param RmaAccess $rmaAccess
     */
    public function __construct(RmaAccess $rmaAccess)
    {
        $this->rmaAccess = $rmaAccess;
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\PackageItem[]
     */
    public function getRmaShipmentItems()
    {
        $packages = $this->rmaAccess->getCurrentRmaShipment()->getPackages();

        $collectPackageItems = function (array $packageItems, PackageInterface $package) {
            return array_merge($packageItems, $package->getItems());
        };

        $allItems = array_reduce($packages, $collectPackageItems, []);
        return $allItems;
    }
}
