<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Pickup;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Helper\Admin;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Temando\Shipping\Model\Location\OrderAddressFactory;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\PickupProviderInterface;
use Temando\Shipping\ViewModel\DataProvider\OrderDate;

/**
 * View model for Pickup related blocks.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Sebastian Ertner<sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupView implements ArgumentInterface
{
    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * @var OrderDate
     */
    private $orderDate;

    /**
     * @var Admin
     */
    private $adminHelper;

    /**
     * @var OrderAddressFactory
     */
    private $orderAddressFactory;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * PickupView constructor.
     * @param PickupProviderInterface $pickupProvider
     * @param OrderDate $orderDate
     * @param Admin $adminHelper
     * @param OrderAddressFactory $orderAddressFactory
     * @param AddressRenderer $addressRenderer
     */
    public function __construct(
        PickupProviderInterface $pickupProvider,
        OrderDate $orderDate,
        Admin $adminHelper,
        OrderAddressFactory $orderAddressFactory,
        AddressRenderer $addressRenderer
    ) {
        $this->pickupProvider = $pickupProvider;
        $this->orderDate = $orderDate;
        $this->adminHelper = $adminHelper;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @param string $type
     * @return string
     */
    private function getDate(string $type): string
    {
        $pickup = $this->pickupProvider->getPickup();
        if (!$pickup) {
            return '';
        }

        switch ($type) {
            case PickupInterface::READY_AT:
                return (string)$pickup->getReadyAt();
            case PickupInterface::PICKED_UP_AT:
                return (string)$pickup->getPickedUpAt();
            case PickupInterface::CANCELLED_AT:
                return (string)$pickup->getCancelledAt();
            default:
                return '';
        }
    }

    /**
     * @return OrderInterface|Order
     */
    public function getOrder(): OrderInterface
    {
        return $this->pickupProvider->getOrder();
    }

    /**
     * @return string
     */
    public function getPickupId(): string
    {
        $pickup = $this->pickupProvider->getPickup();
        if (!$pickup) {
            return '';
        }

        return $pickup->getPickupId();
    }

    /**
     * @return string
     */
    public function getPickupState(): string
    {
        $pickup = $this->pickupProvider->getPickup();
        if (!$pickup) {
            return '';
        }

        return ucwords($pickup->getState());
    }

    /**
     * @return string
     */
    public function getReadyAtDate(): string
    {
        $date = $this->getDate(PickupInterface::READY_AT);
        return $this->orderDate->getAdminDate($date);
    }

    /**
     * @return string
     */
    public function getPickedUpAtDate(): string
    {
        $date = $this->getDate(PickupInterface::PICKED_UP_AT);
        return $this->orderDate->getAdminDate($date);
    }

    /**
     * @return string
     */
    public function getCancelledAtDate(): string
    {
        $date = $this->getDate(PickupInterface::CANCELLED_AT);
        return $this->orderDate->getAdminDate($date);
    }

    /**
     * @return string
     */
    public function getShippingDescription(): string
    {
        $order = $this->getOrder();
        if (!$order) {
            return '';
        }

        return $order->getShippingDescription();
    }

    /**
     * Retrieve subtotal price include tax html formatted content
     *
     * @param OrderInterface $order
     * @return string
     */
    public function displayShippingPriceInclTax(OrderInterface $order): string
    {
        $shipping = $order->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $order->getBaseShippingInclTax();
        } else {
            $shipping = $order->getShippingAmount() + $order->getShippingTaxAmount();
            $baseShipping = $order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount();
        }

        $priceHtml = $this->adminHelper->displayPrices(
            $this->getOrder(),
            $baseShipping,
            $shipping,
            false,
            ' '
        );

        return $priceHtml;
    }

    /**
     * Display price attribute
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>'): string
    {
        $priceHtml = $this->adminHelper->displayPriceAttribute(
            $this->getOrder(),
            $code,
            $strong,
            $separator
        );

        return $priceHtml;
    }

    /**
     * Get the Payment Method title.
     *
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getPaymentMethodTitle(OrderInterface $order): string
    {
        if (!$order->getPayment()) {
            return __('Order has no payment information');
        }

        $paymentData = $order->getPayment()->getData('additional_information');
        return isset($paymentData['method_title']) ? $paymentData['method_title'] : '';
    }

    /**
     * @return string
     */
    public function getPickupLocationAddressHtml(): string
    {
        $pickup = $this->pickupProvider->getPickup();
        if (!$pickup) {
            return '';
        }

        /** @var Address $address */
        $address = $this->orderAddressFactory->createFromShipmentLocation($pickup->getPickupLocation());
        $formattedAddress = $this->addressRenderer->format($address, 'html');
        return (string)$formattedAddress;
    }
}
