<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\DispatchInterface;

/**
 * Temando Dispatch Status
 *
 * @package  Temando\Shipping\Ui
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchStatus extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = DispatchInterface::STATUS;
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$key]) && ($item[$key] !== 'processed')) {
                    $item[$fieldName] = 'pending';
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
