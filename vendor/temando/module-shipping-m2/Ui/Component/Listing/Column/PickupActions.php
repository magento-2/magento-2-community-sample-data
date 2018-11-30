<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\ViewModel\DataProvider\PickupUrl;

/**
 * Temando Packaging Grid Actions
 *
 * @package Temando\Shipping\Ui
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupActions extends Column
{
    /**
     * @var PickupUrl
     */
    private $pickupUrl;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PickupUrl $pickupUrl
     * @param mixed[] $components
     * @param mixed[] $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PickupUrl $pickupUrl,
        array $components = [],
        array $data = []
    ) {
        $this->pickupUrl = $pickupUrl;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Add edit action to grid.
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource)
    {
        $idField = PickupInterface::PICKUP_ID;
        $stateField = PickupInterface::STATE;

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item[$idField])) {
                    if ($item[$stateField] == PickupInterface::STATE_REQUESTED) {
                        $item[$name]['edit'] = [
                            'href' => $this->pickupUrl->getEditActionUrl($item),
                            'label' => __('View')
                        ];
                    } else {
                        $item[$name]['edit'] = [
                            'href' => $this->pickupUrl->getViewActionUrl($item),
                            'label' => __('View')
                        ];
                    }
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
