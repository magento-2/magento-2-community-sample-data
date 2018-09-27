<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\CarrierInterface;

/**
 * Temando Carrier Grid Logo Column
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierLogo extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $altField = $this->getData('config/altField');
        $altFallbackField = CarrierInterface::NAME;

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $src = isset($item[$fieldName]) ? $item[$fieldName] : '#';
                $alt = isset($item[$altField]) ? $item[$altField] : 'Carrier Logo';
                if (!$alt && isset($item[$altFallbackField])) {
                    $alt = $item[$altFallbackField];
                }

                $item[$fieldName . '_src'] = $src;
                $item[$fieldName . '_alt'] = $alt;
            }
        }

        return $dataSource;
    }
}
