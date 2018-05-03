<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Dispatch;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Container;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Sales\Api\Data\ShipmentSearchResultInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Shipment;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\Dispatch\ShipmentInterface;
use Temando\Shipping\Model\ResourceModel\Shipment\ShipmentReference as ShipmentReferenceResource;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\DispatchProviderInterface;

/**
 * Temando Dispatch Solve Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class Solve extends Container
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var DispatchProviderInterface
     */
    private $dispatchProvider;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipmentReferenceResource
     */
    private $shipmentReferenceResource;

    /**
     * Solve constructor.
     *
     * @param Context $context
     * @param ModuleConfigInterface $config
     * @param DispatchProviderInterface $dispatchProvider
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentReferenceResource $shipmentReferenceResource
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        ModuleConfigInterface $config,
        DispatchProviderInterface $dispatchProvider,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentReferenceResource $shipmentReferenceResource,
        array $data = []
    ) {
        $this->config = $config;
        $this->dispatchProvider = $dispatchProvider;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentReferenceResource = $shipmentReferenceResource;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
                'class' => 'back'
            ],
            -1
        );
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        $dispatch = $this->getDispatch();

        return $dispatch
            ? $this->_urlBuilder->getUrl(
                'temando/dispatch/view',
                ['dispatch_id' => $dispatch->getDispatchId()]
            )
            : $this->_urlBuilder->getUrl('temando/dispatch/index');
    }

    /**
     * @return string
     */
    public function getNewUrl()
    {
        return $this->_urlBuilder->getUrl('temando/dispatch/new');
    }

    /**
     * @param string $shipmentId
     * @return string
     */
    public function getShipmentUrl($shipmentId)
    {
        return $this->_urlBuilder->getUrl('sales/shipment/view', ['shipment_id' => $shipmentId]);
    }

    /**
     * @return ShipmentSearchResultInterface|\Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     */
    public function getFailedShipments()
    {
        $failedShipments = $this->dispatchProvider->getDispatch()->getFailedShipments();
        $shipmentIds = $this->shipmentReferenceResource->getShipmentIdsByExtShipmentIds(array_keys($failedShipments));

        $filter = $this->filterBuilder
            ->setField('entity_id')
            ->setValue($shipmentIds)
            ->setConditionType('in')
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($filter)
            ->addSortOrder('entity_id', SortOrder::SORT_ASC)
            ->create();

        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $salesShipments */
        $salesShipments = $this->shipmentRepository->getList($searchCriteria);

        $extShipmentIds = array_flip($shipmentIds);
        $addExtShipmentProperties = function (Shipment $salesShipment) use ($extShipmentIds, $failedShipments) {
            $extShipmentId = $extShipmentIds[$salesShipment->getId()];
            $salesShipment->setData(ShipmentInterface::MESSAGE, $failedShipments[$extShipmentId]->getMessage());
            $salesShipment->setData(ShipmentInterface::SHIPMENT_ID, $extShipmentId);
        };
        $salesShipments->walk($addExtShipmentProperties);

        return $salesShipments;
    }

    /**
     * @return string
     */
    public function getShippingPortalUrl()
    {
        return $this->config->getShippingPortalUrl();
    }

    /**
     * @return DispatchInterface|null
     */
    public function getDispatch()
    {
        return $this->dispatchProvider->getDispatch();
    }
}
