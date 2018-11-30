<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Location\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\Model\ResourceModel\Repository\LocationRepositoryInterface;

/**
 * Temando Location Option Source
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class LocationOptionSource implements OptionSourceInterface
{
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * LocationOptionSource constructor.
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = array_map(function (LocationInterface $location) {
            return [
                'value' => $location->getLocationId(),
                'label' => $location->getName(),
            ];
        }, $this->locationRepository->getList());

        return $options;
    }
}
