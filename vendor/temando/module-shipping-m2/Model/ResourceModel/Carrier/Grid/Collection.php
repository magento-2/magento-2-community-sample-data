<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Carrier\Grid;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CarrierRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Webservice\Collection as ApiCollection;

/**
 * Temando Carrier Resource Collection
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
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param CarrierRepositoryInterface $carrierRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CarrierRepositoryInterface $carrierRepository
    ) {
        $this->carrierRepository = $carrierRepository;

        parent::__construct($entityFactory, $messageManager, $filterBuilder, $searchCriteriaBuilder);
    }

    /**
     * @param SearchCriteriaInterface $criteria
     *
     * @return CarrierInterface[]
     */
    public function fetchData(SearchCriteriaInterface $criteria)
    {
        $carriers = $this->carrierRepository->getList(
            $criteria->getCurrentPage(),
            $criteria->getPageSize()
        );

        return $carriers;
    }
}
