<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Packaging\Grid;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Temando\Shipping\Model\PackagingInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PackagingRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Webservice\Collection as ApiCollection;

/**
 * Temando Packaging Resource Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Collection extends ApiCollection
{
    /**
     * @var PackagingRepositoryInterface
     */
    private $packagingRepository;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param PackagingRepositoryInterface $packagingRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PackagingRepositoryInterface $packagingRepository
    ) {
        $this->packagingRepository = $packagingRepository;

        parent::__construct($entityFactory, $messageManager, $filterBuilder, $searchCriteriaBuilder);
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @return PackagingInterface[]
     */
    public function fetchData(SearchCriteriaInterface $criteria)
    {
        $containers = $this->packagingRepository->getList(
            $criteria->getCurrentPage(),
            $criteria->getPageSize()
        );

        return $containers;
    }
}
