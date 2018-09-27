<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\CarrierInterface;

/**
 * Temando Carrier Grid Actions
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierActions extends Column
{
    /**
     * Add edit action to grid.
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');

                if (isset($item[CarrierInterface::CONFIGURATION_ID])
                    && isset($item[CarrierInterface::INTEGRATION_ID])
                ) {
                    $editUrl = $this->getContext()->getUrl('temando/configuration_carrier/edit', [
                        CarrierInterface::CONFIGURATION_ID => $item[CarrierInterface::CONFIGURATION_ID],
                        CarrierInterface::INTEGRATION_ID => $item[CarrierInterface::INTEGRATION_ID]
                    ]);

                    $deleteUrl = $this->getContext()->getUrl('temando/configuration_carrier/delete', [
                        CarrierInterface::CONFIGURATION_ID => $item[CarrierInterface::CONFIGURATION_ID]
                    ]);

                    $item[$name]['edit'] = [
                        'href'  => $editUrl,
                        'label' => __('Configure')
                    ];
                    $item[$name]['delete'] = [
                        'href'    => $deleteUrl,
                        'label'   => __('Delete'),
                        'confirm' => [
                            'title'   => __('Delete "${ $.$data.name }"'),
                            'message' => __('Are you sure you want to delete the carrier "${ $.$data.name }"?')
                        ]
                    ];
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
