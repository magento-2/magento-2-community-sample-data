<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\Builder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;

/**
 * Build a Guest Cart
 */
class GuestCartBuilder
{
    /** @var CartItemInterfaceFactory */
    private $cartItemFactory;

    /** @var GuestCartManagementInterface */
    private $cartManager;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var GuestCartRepositoryInterface */
    private $guestCartRepository;

    /** @var CartItemInterface[] */
    private $items = [];

    /**
     * @param CartItemInterfaceFactory $cartItemFactory
     * @param GuestCartManagementInterface $cartManager
     * @param GuestCartRepositoryInterface $guestCartRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartItemInterfaceFactory $cartItemFactory,
        GuestCartManagementInterface $cartManager,
        GuestCartRepositoryInterface $guestCartRepository,
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartItemFactory = $cartItemFactory;
        $this->cartManager = $cartManager;
        $this->guestCartRepository = $guestCartRepository;
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
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function create()
    {
        $cartId = $this->cartManager->createEmptyCart();
        $cart = $this->guestCartRepository->get($cartId);
        $cart->setItems($this->items);
        $this->cartRepository->save($cart);

        return $cart;
    }

    /**
     * Set the cart items
     *
     * @param CartInterface[] $items
     * @return $this
     */
    public function setItems(array $items = [])
    {
        $this->items = $items;

        return $this;
    }
}
