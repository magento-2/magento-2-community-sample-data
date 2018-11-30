<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * View model for RMA shipment dispatch creation.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchCreate implements ArgumentInterface, PageActionsInterface, ShippingApiInterface
{
    /**
     * @var ShippingApiAccess
     */
    private $apiAccess;

    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * DispatchCreate constructor.
     * @param ShippingApiAccess $apiAccess
     * @param RmaAccess $rmaAccess
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ShippingApiAccess $apiAccess,
        RmaAccess $rmaAccess,
        ShipmentRepositoryInterface $shipmentRepository,
        UrlInterface $urlBuilder
    ) {
        $this->apiAccess = $apiAccess;
        $this->rmaAccess = $rmaAccess;
        $this->shipmentRepository = $shipmentRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Obtain array of button data.
     *
     * @see \Temando\Shipping\Block\Adminhtml\ComponentContainer::_prepareLayout
     * @see \Magento\Backend\Block\Widget\Button\ButtonList::add
     *
     * @return mixed[][]
     */
    public function getMainActions(): array
    {
        $buttonId = 'back';
        $buttonData = [
            'label' => __('Back'),
            'onclick' => sprintf("window.location.href = '%s';", $this->getRmaShipmentPageUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];

        $mainActions = [
            $buttonId => $buttonData,
        ];

        return $mainActions;
    }

    /**
     * @return ShippingApiAccessInterface
     */
    public function getShippingApiAccess(): ShippingApiAccessInterface
    {
        return $this->apiAccess;
    }

    /**
     * @return string
     */
    public function getRmaShipmentId(): string
    {
        $rmaShipment = $this->rmaAccess->getCurrentRmaShipment();
        return $rmaShipment->getShipmentId();
    }

    /**
     * @return string
     */
    public function getRmaShipmentPageUrl(): string
    {
        $rmaShipmentId = $this->getRmaShipmentId();
        $routeParams = ['ext_shipment_id' => $rmaShipmentId];

        $rma = $this->rmaAccess->getCurrentRma();
        if (!$rma->getEntityId()) {
            // forward-fulfillment return shipment
            $shipment = $this->shipmentRepository->getReferenceByExtReturnShipmentId($rmaShipmentId);
            $routeParams['shipment_id'] = $shipment->getShipmentId();
        } else {
            // ad-hoc return shipment
            $routeParams['rma_id'] = $rma->getEntityId();
        }

        return $this->urlBuilder->getUrl('temando/rma_shipment/view', $routeParams);
    }

    /**
     * @return string
     */
    public function getDispatchViewPageUrlTpl(): string//http://m2-enterprise-dev.temando.io/admin/temando/dispatch/view/dispatch_id/0a57b79e-c747-4e80-a6bb-45b3aeb5e90d/
    {
        return $this->urlBuilder->getUrl('temando/dispatch/view/dispatch_id/--id--/');
    }

    /**
     * @return string
     */
    public function getDispatchGridPageUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/dispatch/index');
    }
}
