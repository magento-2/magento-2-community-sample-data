<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Total Quantity Grid Renderer.
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class TotalPackages extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = ShipmentInterface::PACKAGES;
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$key])) {
                    /** @var PackageInterface[] $packages */
                    $packages   = $item[$key];
                    $totalQty   = count($packages);
                    $item[$key] = $totalQty;
                }
            }
        }

        return $dataSource;
    }
}
