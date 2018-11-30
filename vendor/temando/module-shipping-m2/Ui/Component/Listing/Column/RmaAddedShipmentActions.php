<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentReferenceRepositoryInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Rma Shipment Grid Actions
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RmaAddedShipmentActions extends Column
{
    const RMA_SHIPMENT_URL_PATH_VIEW = 'temando/rma_shipment/view';
    const RMA_SHIPMENT_URL_PATH_REMOVE = 'temando/rma_shipment_forward/remove';

    /**
     * @var ShipmentReferenceRepositoryInterface
     */
    private $shipmentReferenceRepository;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ShipmentReferenceRepositoryInterface $shipmentReferenceRepository
     * @param mixed[] $components
     * @param mixed[] $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShipmentReferenceRepositoryInterface $shipmentReferenceRepository,
        array $components = [],
        array $data = []
    ) {
        $this->shipmentReferenceRepository = $shipmentReferenceRepository;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Add view action to grid.
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = ShipmentInterface::SHIPMENT_ID;
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item[$key])) {
                    $rmaId = $this->getContext()->getRequestParam('id');
                    $extShipmentId = $item[$key];
                    $item[$name]['view'] = [
                        'href' => $this->getContext()->getUrl(
                            self::RMA_SHIPMENT_URL_PATH_VIEW,
                            [
                                'rma_id' => $rmaId,
                                'ext_shipment_id' => $extShipmentId
                            ]
                        ),
                        'label' => __('View')
                    ];

                    try {
                        $shipmentReference = $this->shipmentReferenceRepository
                            ->getByExtReturnShipmentId($extShipmentId);
                    } catch (LocalizedException $e) {
                        $shipmentReference = null;
                    }

                    // only forward fulfillment return shipments can be removed.
                    if ($shipmentReference) {
                        $item[$name]['remove'] = [
                            'href' => $this->getContext()->getUrl(
                                self::RMA_SHIPMENT_URL_PATH_REMOVE,
                                [
                                    'rma_id' => $rmaId,
                                    'ext_shipment_id' => $extShipmentId
                                ]
                            ),
                            'label' => __('Remove')
                        ];
                    }
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
