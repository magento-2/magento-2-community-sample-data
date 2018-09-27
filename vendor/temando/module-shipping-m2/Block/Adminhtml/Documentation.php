<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml;

use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Backend\Block\Template\Context;
use Temando\Shipping\Model\DispatchProviderInterface;
use Temando\Shipping\Model\DocumentationInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * Temando Documentation Listing Layout Block
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
class Documentation extends BackendTemplate
{
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
     * @param Context $context
     * @param ShipmentProviderInterface $shipmentProvider
     * @param DispatchProviderInterface $dispatchProvider
     * @param RmaAccess $rmaAccess
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        ShipmentProviderInterface $shipmentProvider,
        DispatchProviderInterface $dispatchProvider,
        RmaAccess $rmaAccess,
        array $data = []
    ) {
        $this->shipmentProvider = $shipmentProvider;
        $this->dispatchProvider = $dispatchProvider;
        $this->rmaAccess = $rmaAccess;

        parent::__construct($context, $data);
    }

    /**
     * Set documentation from
     * - dispatch
     * - shipment or
     * - rma shipment
     *
     * @return BackendTemplate
     */
    protected function _beforeToHtml()
    {
        if (!$this->hasData('documentation')) {
            if ($this->dispatchProvider->getDispatch()) {
                $dispatch = $this->dispatchProvider->getDispatch();
                $this->setData('documentation', $dispatch->getDocumentation());
            } elseif ($this->shipmentProvider->getShipment()) {
                $shipment = $this->shipmentProvider->getShipment();
                $this->setData('documentation', $shipment->getDocumentation());
            } elseif ($this->rmaAccess->getCurrentRmaShipment()) {
                $shipment = $this->rmaAccess->getCurrentRmaShipment();
                $this->setData('documentation', $shipment->getDocumentation());
            }
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return DocumentationInterface[]
     */
    public function getDocumentation()
    {
        return $this->hasData('documentation') ? $this->getData('documentation') : [];
    }

    /**
     * @param DocumentationInterface $documentation
     * @return \Magento\Framework\Phrase
     */
    public function getDisplayName(DocumentationInterface $documentation)
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

        $displayName = isset($fileTypeNames[$documentation->getType()])
            ? $fileTypeNames[$documentation->getType()]
            : $documentation->getType();

        return __($displayName);
    }

    /**
     * @return bool
     */
    public function isPaperless()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        if ($shipment) {
            $originCountryCode = $shipment->getOriginLocation()->getCountryCode();
            $destinationCountryCode = $shipment->getDestinationLocation()->getCountryCode();

            if (($originCountryCode != $destinationCountryCode) && $shipment->isPaperless()) {
                return true;
            }
        }

        return false;
    }
}
