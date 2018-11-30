<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Repository;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Repository of Tax Class Names
 */
class TaxClassNameRepository
{
    /** @var SearchCriteriaBuilderFactory */
    private $criteriaBuilderFactory;

    /** @var GroupRepositoryInterface */
    private $customerGroupRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var TaxClassRepositoryInterface */
    private $repository;

    /**
     * @param TaxClassRepositoryInterface $repository
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilderFactory $criteriaBuilderFactory
     * @param GroupRepositoryInterface $customerGroupRepository
     */
    public function __construct(
        TaxClassRepositoryInterface $repository,
        LoggerInterface $logger,
        SearchCriteriaBuilderFactory $criteriaBuilderFactory,
        GroupRepositoryInterface $customerGroupRepository
    ) {
        $this->repository = $repository;
        $this->logger = $logger;
        $this->criteriaBuilderFactory = $criteriaBuilderFactory;
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * Get the name of a Tax Class given a Customer Group it's assigned to
     *
     * @param int $customerGroupId
     * @return string
     */
    public function getByCustomerGroupId($customerGroupId)
    {
        try {
            $classId = $this->customerGroupRepository->getById($customerGroupId)->getTaxClassId();
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            $classId = 0;
        }
        return $this->getById($classId);
    }

    /**
     * Get the Tax Class Name given it's ID - or None if it could not be found
     *
     * @param int $taxClassId
     * @return string
     */
    public function getById($taxClassId)
    {
        if (!$taxClassId) {
            return 'None';
        }

        try {
            return $this->repository->get($taxClassId)
                ->getClassName();
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            return 'None';
        }
    }

    /**
     * Get an array of Tax Class Names given an array of Tax Class IDs
     *
     * Will provide "None" when a tax class is not found for any given ID
     *
     * @param int[] $taxClassIds
     * @return string[] Indexed by tax class id
     */
    public function getListByIds(array $taxClassIds)
    {
        /** @var SearchCriteriaBuilder $criteriaBuilder */
        $criteriaBuilder = $this->criteriaBuilderFactory->create();
        $criteriaBuilder->addFilter('class_id', $taxClassIds, 'in');
        $criteria = $criteriaBuilder->create();

        try {
            $list = $this->repository->getList($criteria);
        } catch (InputException $exception) {
            $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
        $result = [];
        foreach ($list->getItems() as $item) {
            $result[$item->getClassId()] = $item->getClassName();
        }

        foreach ($taxClassIds as $classId) {
            if (!isset($result[$classId])) {
                $result[$classId] = 'None';
            }
        }

        return $result;
    }
}
