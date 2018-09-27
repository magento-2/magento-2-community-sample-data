<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\CarrierInterface;

/**
 * Temando Carrier Service List
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierServices extends Column
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * CarrierServices constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param string[] $services
     * @return string
     */
    private function renderServiceList(array $services)
    {
        if (empty($services)) {
            return '';
        }

        $serviceNames = implode(', ', $services);

        return $this->escaper->escapeHtml($serviceNames);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = CarrierInterface::ACTIVE_SERVICES;
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$key])) {
                    $item[$fieldName] = $this->renderServiceList($item[$fieldName]);
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
