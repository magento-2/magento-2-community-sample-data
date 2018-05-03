<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\DispatchInterface;

/**
 * Temando Dispatch Documentation List
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchActions extends Column
{
    const DISPATCH_URL_PATH_VIEW = 'temando/dispatch/view';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = DispatchInterface::DISPATCH_ID;
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$key])) {
                    if ($item['status'] !== 'processed') {
                        // @codingStandardsIgnoreLine
                        $linkTemplate = '<a href="#" onClick="require(\'uiRegistry\').get(\'%s.%s\').set(\'params.t\', Date.now()); return false;">%s</a>';
                        $cellHtml = sprintf(
                            $linkTemplate,
                            $this->getContext()->getNamespace(),
                            $this->getContext()->getDataProvider()->getName(),
                            __('Reload')
                        );
                    } else {
                        $linkTemplate = '<a href="%s">%s</a>';
                        $url = $this->getContext()->getUrl(self::DISPATCH_URL_PATH_VIEW, [
                            'dispatch_id' => $item[$key],
                        ]);
                        $cellHtml = sprintf($linkTemplate, $url, __('View'));
                    }

                    $item[$fieldName] = $cellHtml;
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
