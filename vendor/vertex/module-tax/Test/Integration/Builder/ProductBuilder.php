<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\Builder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\StockRegistryStorage;

/**
 * Build a product with stock
 */
class ProductBuilder
{
    /** @var ProductInterfaceFactory */
    private $productFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var StockRegistryInterface */
    private $stockRegistry;

    /** @var StockRegistryStorage */
    private $stockRegistryStorage;

    /**
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param StockRegistryStorage $stockRegistryStorage
     */
    public function __construct(
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        StockRegistryStorage $stockRegistryStorage
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->stockRegistryStorage = $stockRegistryStorage;
    }

    /**
     * Create a product including stock
     *
     * Performs 3 database queries.
     *
     * @param callable $productConfiguration Receives 1 parameter of ProductInterface.  Should return a ProductInterface
     * @param bool $isInStock
     * @param int $stockQty
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createProduct(callable $productConfiguration, $isInStock = true, $stockQty = 500)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->create();
        $product = $productConfiguration($product);
        if (!($product instanceof ProductInterface)) {
            throw new \TypeError('Result of createProduct callback must return a ProductInterface');
        }

        $product = $this->productRepository->save($product);

        $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
        $stockItem->setIsInStock($isInStock);
        $stockItem->setQty($stockQty);
        $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);

        // Reload product in such a way as to re-generate saleability data
        $this->stockRegistryStorage->removeStockStatus($product->getId());
        $this->stockRegistryStorage->removeStockItem($product->getId());
        $product = $this->productRepository->get($product->getSku());

        return $product;
    }
}
