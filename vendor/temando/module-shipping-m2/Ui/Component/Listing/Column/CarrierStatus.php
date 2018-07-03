<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\CarrierInterface;

/**
 * Temando Carrier Status
 *
 * @package  Temando\Shipping\Ui
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierStatus extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = CarrierInterface::STATUS;
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                switch ($item[$key]) {
                    case 'disabled':
                        $item[$fieldName] = __('Disabled');
                        break;
                    case 'active':
                        $item[$fieldName] = __('Active');
                        break;
                    default:
                        $item[$fieldName] = __('Pending');
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
