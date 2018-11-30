<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\Builder;

use Magento\Tax\Api\Data\TaxClassInterface;
use Magento\Tax\Api\Data\TaxClassInterfaceFactory;
use Magento\Tax\Api\TaxClassRepositoryInterface;

/**
 * Build a Tax Class
 */
class TaxClassBuilder
{
    /** @var TaxClassInterfaceFactory */
    private $factory;

    /** @var TaxClassRepositoryInterface */
    private $repository;

    /**
     * @param TaxClassInterfaceFactory $factory
     * @param TaxClassRepositoryInterface $repository
     */
    public function __construct(TaxClassInterfaceFactory $factory, TaxClassRepositoryInterface $repository)
    {
        $this->factory = $factory;
        $this->repository = $repository;
    }

    /**
     * Create and save a new tax class
     *
     * @param string $taxClassName
     * @param string $taxClassType One of PRODUCT or CATEGORY
     * @return string Tax Class ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createTaxClass($taxClassName, $taxClassType = 'PRODUCT')
    {
        /** @var TaxClassInterface $taxClass */
        $taxClass = $this->factory->create();
        $taxClass->setClassType($taxClassType);
        $taxClass->setClassName($taxClassName);

        return $this->repository->save($taxClass);
    }
}
