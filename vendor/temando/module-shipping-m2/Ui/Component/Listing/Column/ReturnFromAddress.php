<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\Listing\Column;

use Magento\Directory\Model\Region;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Temando\Shipping\Model\Shipment\LocationInterface;
use Temando\Shipping\Model\ShipmentInterface;

/**
 * Temando Return from Address Grid Column Renderer.
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ReturnFromAddress extends Column
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var Region
     */
    private $region;

    /**
     * ReturnFromAddress constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param Region $region
     * @param mixed[] $components
     * @param mixed[]  $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        Region $region,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->region = $region;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $key = ShipmentInterface::ORIGIN_LOCATION;
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$key])) {
                    /** @var LocationInterface $originLocation */
                    $originLocation = $item[$key];
                    $address = sprintf(
                        '%s %s %s %s',
                        $this->getStreet($originLocation),
                        $this->getRegionName($originLocation),
                        $originLocation->getCity(),
                        $originLocation->getPostalCode()
                    );
                    $item[$key] = $this->escaper->escapeHtml(
                        str_replace("\n", '<br/>', $address)
                    );
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get street as string
     *
     * @param LocationInterface $originLocation
     * @return string
     */
    private function getStreet(LocationInterface $originLocation)
    {
        $street = '';
        foreach ($originLocation->getStreet() as $streetLine) {
            $street.= $streetLine . ' ';
        }

        return $street;
    }

    /**
     * Get Region Name.
     *
     * @param LocationInterface $originLocation
     * @return string
     */
    private function getRegionName(LocationInterface $originLocation)
    {
        $region = $this->region->loadByCode(
            $originLocation->getRegionCode(),
            $originLocation->getCountryCode()
        );

        return $region->getName();
    }
}
