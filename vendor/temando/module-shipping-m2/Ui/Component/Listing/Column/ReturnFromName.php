<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\Shipment\LocationInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Return From Name Grid Column Renderer.
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ReturnFromName extends Column
{
    /**
     * Extract "Return From Name" from origin location.
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = ShipmentInterface::ORIGIN_LOCATION;
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$key])) {
                    /** @var LocationInterface $originLocation */
                    $originLocation = $item[$key];
                    $item[$fieldName] = sprintf(
                        '%s %s',
                        $originLocation->getPersonFirstName(),
                        $originLocation->getPersonLastName()
                    );
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
