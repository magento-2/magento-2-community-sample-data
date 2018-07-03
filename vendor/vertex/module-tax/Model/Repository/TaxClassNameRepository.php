<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Repository;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Repository of Tax Class Names
 */
class TaxClassNameRepository
{
    /** @var TaxClassRepositoryInterface */
    private $repository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param TaxClassRepositoryInterface $repository
     * @param LoggerInterface $logger
     */
    public function __construct(TaxClassRepositoryInterface $repository, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
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
}
