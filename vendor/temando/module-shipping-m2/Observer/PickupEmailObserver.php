<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Temando\Shipping\Model\Delivery\OpeningHoursFormatter;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\PickupProviderInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderPickupLocationRepositoryInterface;
use Temando\Shipping\ViewModel\Order\Location;
use Temando\Shipping\ViewModel\Pickup\PickupView;

/**
 * Temando Pickup Email Observer
 *
 * @package Temando\Shipping\Observer
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupEmailObserver implements ObserverInterface
{
    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * @var PickupView
     */
    private $pickupViewModel;

    /**
     * @var OrderPickupLocationRepositoryInterface
     */
    private $pickupLocationRepository;

    /**
     * @var Location
     */
    private $locationViewModel;

    /**
     * @var OpeningHoursFormatter
     */
    private $hoursFormatter;

    /**
     * PickupEmailObserver constructor.
     * @param PickupProviderInterface $pickupProvider
     * @param PickupView $pickupViewModel
     * @param OrderPickupLocationRepositoryInterface $pickupLocationRepository
     * @param Location $locationViewModel
     * @param OpeningHoursFormatter $hoursFormatter
     */
    public function __construct(
        PickupProviderInterface $pickupProvider,
        PickupView $pickupViewModel,
        OrderPickupLocationRepositoryInterface $pickupLocationRepository,
        Location $locationViewModel,
        OpeningHoursFormatter $hoursFormatter
    ) {
        $this->pickupProvider = $pickupProvider;
        $this->pickupViewModel = $pickupViewModel;
        $this->pickupLocationRepository = $pickupLocationRepository;
        $this->locationViewModel = $locationViewModel;
        $this->hoursFormatter = $hoursFormatter;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $sender = $observer->getData('sender');
        if (!$sender instanceof OrderSender) {
            // not interested in other sales entities but orders
            return;
        }

        /** @var DataObject $transport */
        $transport = $observer->getData('transportObject');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $transport->getData('order');
        if (!$order || !$order->getShippingAddress()) {
            return;
        }

        $pickup = $this->pickupProvider->getPickup();
        if ($pickup instanceof PickupInterface) {
            $location = $pickup->getPickupLocation();
            $openingHours = $this->hoursFormatter->format($location->getOpeningHours());

            // pickup action emails
            $transport->setData('is_pickup_order', true);
            $transport->setData('pickupAddress', $this->pickupViewModel->getPickupLocationAddressHtml());
            $transport->setData('pickup', $pickup);
            $transport->setData('openingHours', $openingHours);
            return;
        }

        try {
            // pickup order confirmation mail
            $pickup = $this->pickupLocationRepository->get($order->getShippingAddress()->getId());
            $transport->setData('is_pickup_order', (bool)$pickup->getPickupLocationId());
            $transport->setData('pickupAddress', $this->locationViewModel->getDeliveryLocationAddressHtml($order));
        } catch (NoSuchEntityException $e) {
            // regular order confirmation mail
            $transport->setData('is_pickup_order', false);
        }
    }
}
