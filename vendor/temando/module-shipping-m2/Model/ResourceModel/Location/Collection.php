<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Location;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\Model\ResourceModel\Repository\LocationRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Webservice\Collection as ApiCollection;

/**
 * Temando Location Resource Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Collection extends ApiCollection
{
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        LocationRepositoryInterface $locationRepository
    ) {
        $this->locationRepository = $locationRepository;

        parent::__construct($entityFactory, $messageManager);
    }

    /**
     * @return LocationInterface[]
     */
    public function fetchData()
    {
        $locations = $this->locationRepository->getList();
        return $locations;
    }
}
