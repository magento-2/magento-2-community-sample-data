<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\Builder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;

/**
 * Build a Cart
 */
class CartBuilder
{
    /** @var CartItemInterfaceFactory */
    private $cartItemFactory;

    /** @var CartManagementInterface */
    private $cartManager;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var CartItemInterface[] */
    private $items = [];

    /**
     * @param CartItemInterfaceFactory $cartItemFactory
     * @param CartManagementInterface $cartManager
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartItemInterfaceFactory $cartItemFactory,
        CartManagementInterface $cartManager,
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartItemFactory = $cartItemFactory;
        $this->cartManager = $cartManager;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Add a product to the cart
     *
     * @param ProductInterface $product
     * @param int $qty Default 1
     * @return $this
     */
    public function addItem(ProductInterface $product, $qty = 1)
    {
        /** @var CartItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->create();
        $cartItem->setSku($product->getSku());
        $cartItem->setName($product->getName());
        $cartItem->setQty($qty);
        $cartItem->setPrice($product->getPrice());
        $cartItem->setProductType($product->getTypeId());

        $this->items[] = $cartItem;

        return $this;
    }

    /**
     * Build the Cart
     *
     * @param int $customerId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function create($customerId)
    {
        $cartId = $this->cartManager->createEmptyCartForCustomer($customerId);
        $cart = $this->cartRepository->get($cartId);
        $cart->setItems($this->items);
        $this->cartRepository->save($cart);

        return $cart;
    }

    /**
     * Set the cart items
     *
     * @param CartItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items = [])
    {
        $this->items = $items;

        return $this;
    }
}
