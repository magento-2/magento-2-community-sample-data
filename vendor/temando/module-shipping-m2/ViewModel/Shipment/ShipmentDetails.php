<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Shipment;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\DispatchProviderInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Model\ShipmentInterfaceFactory;

/**
 * View model for shipment related information.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class ShipmentDetails implements ArgumentInterface
{
    /**
     * @var ShipmentInterfaceFactory
     */
    private $shipmentFactory;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var DispatchProviderInterface
     */
    private $dispatchProvider;

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ShipmentDetails constructor.
     * @param ShipmentInterfaceFactory $shipmentFactory
     * @param ShipmentProviderInterface $shipmentProvider
     * @param DispatchProviderInterface $dispatchProvider
     * @param RmaAccess $rmaAccess
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ShipmentInterfaceFactory $shipmentFactory,
        ShipmentProviderInterface $shipmentProvider,
        DispatchProviderInterface $dispatchProvider,
        RmaAccess $rmaAccess,
        UrlInterface $urlBuilder
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentProvider = $shipmentProvider;
        $this->dispatchProvider = $dispatchProvider;
        $this->rmaAccess = $rmaAccess;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return ShipmentInterface
     */
    private function getShipment() : ShipmentInterface
    {
        if ($this->shipmentProvider->getShipment()) {
            return $this->shipmentProvider->getShipment();
        }

        if ($this->rmaAccess->getCurrentRmaShipment()) {
            return $this->rmaAccess->getCurrentRmaShipment();
        }

        return $this->shipmentFactory->create();
    }

    /**
     * @param string $extShipmentId
     * @return string
     */
    public function getViewActionUrl($extShipmentId)
    {
        return $this->urlBuilder->getUrl('temando/shipment/view', ['shipment_id' => $extShipmentId]);
    }

    /**
     * @return string
     */
    public function getExtShipmentId()
    {
        $shipment = $this->getShipment();
        return ($shipment ? (string) $shipment->getShipmentId() : '');
    }

    /**
     * @return string
     */
    public function getCustomerReference()
    {
        $shipment = $this->getShipment();
        return ($shipment ? (string) $shipment->getCustomerReference() : '');
    }

    /**
     * @return DataObject[]
     */
    public function getDocumentation()
    {
        if ($this->dispatchProvider->getDispatch()) {
            $dispatch = $this->dispatchProvider->getDispatch();
            $documentation = $dispatch->getDocumentation();
        } else {
            $shipment = $this->getShipment();
            $documentation = $shipment->getDocumentation();
        }

        /** @var DataObject[] $documentation */
        return $documentation ?: [];
    }

    /**
     * @return DataObject[]
     */
    public function getPackages()
    {
        /** @var DataObject[] $packages */
        $packages = $this->getShipment()->getPackages();
        return $packages ?: [];
    }

    /**
     * @return DataObject[]
     */
    public function getItems()
    {
        $packages = $this->getShipment()->getPackages() ?: [];

        $items = array_reduce($packages, function (array $items, PackageInterface $package) {
            $items = array_merge($items, $package->getItems());
            return $items;
        }, []);

        return $items;
    }

    /**
     * @param string $documentationType
     * @return \Magento\Framework\Phrase
     */
    public function getDocumentationDisplayName($documentationType)
    {
        $fileTypeNames = [
            'nafta' => 'NAFTA',
            'certificateOfOrigin' => 'Certificate Of Origin',
            'cn22' => 'CN 22',
            'cn23' => 'CN 23',
            'codTurnInPage' => 'Cash On Delivery Turn In Page',
            'commercialInvoice' => 'Commercial Invoice',
            'customerInvoice' => 'Customer Invoice',
            'highValueReport' => 'High Value Report',
            'manifestSummary' => 'Manifest Summary',
            'packageLabel' => 'Package Label',
            'packageReturnLabel' => 'Package Return Label',
            'packagingList' => 'Packaging List',
            'proofOfDelivery' => 'Proof Of Delivery'
        ];

        $displayName = isset($fileTypeNames[$documentationType])
            ? $fileTypeNames[$documentationType]
            : $documentationType;

        return __($displayName);
    }

    /**
     * @return bool
     */
    public function isShipmentPaperless()
    {
        $shipment = $this->getShipment();
        if ($shipment->getShipmentId()) {
            $originCountryCode = $shipment->getOriginLocation()->getCountryCode();
            $destinationCountryCode = $shipment->getDestinationLocation()->getCountryCode();

            if (($originCountryCode != $destinationCountryCode) && $shipment->isPaperless()) {
                return true;
            }
        }

        return false;
    }
}
