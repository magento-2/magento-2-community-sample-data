<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
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
class RmaAvailableShipmentActions extends Column
{
    const RMA_SHIPMENT_URL_PATH_ADD = 'temando/rma_shipment_forward/add';

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
                    $item[$name]['add'] = [
                        'href' => $this->getContext()->getUrl(
                            self::RMA_SHIPMENT_URL_PATH_ADD,
                            [
                                'rma_id' => $rmaId,
                                'ext_shipment_id' => $extShipmentId
                            ]
                        ),
                        'label' => __('Add')
                    ];
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
