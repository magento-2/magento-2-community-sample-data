<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Shipping\View;

use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Backend\Block\Template\Context;
use Temando\Shipping\Model\Shipment\ExportDeclarationInterface;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Shipment Details Listing Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class ExportDetails extends BackendTemplate
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
     * @return ExportDeclarationInterface | null
     */
    public function getExportDeclaration()
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentProvider->getShipment();
        return ($shipment ? $shipment->getExportDeclaration() : null);
    }
}
