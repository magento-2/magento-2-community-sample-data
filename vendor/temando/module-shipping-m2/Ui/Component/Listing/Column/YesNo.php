<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Temando Bool Grid Renderer
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class YesNo extends Column
{
    /**
     * Convert boolean value to Yes/No text output.
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $item[$name] = ($item[$name] ?  __('Yes') : __('No'));
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
