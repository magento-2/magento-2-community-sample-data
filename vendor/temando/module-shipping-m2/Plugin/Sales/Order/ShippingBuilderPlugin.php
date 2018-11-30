<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Sales\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShippingExtensionFactory;
use Magento\Sales\Api\Data\ShippingInterface;
use Magento\Sales\Model\Order\ShippingBuilder;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderRepositoryInterface;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Load Temando order id and the shipping experience as selected during checkout.
 *
 * @package  Temando\Shipping\Plugin
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShippingBuilderPlugin
{
    /**
     * @var ShippingExtensionFactory
     */
    private $shippingExtensionFactory;

    /**
     * @var ShippingExperienceInterfaceFactory
     */
    private $shippingExperienceFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * ShippingBuilderPlugin constructor.
     * @param ShippingExtensionFactory $shippingExtensionFactory
     * @param ShippingExperienceInterfaceFactory $shippingExperienceFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     */
    public function __construct(
        ShippingExtensionFactory $shippingExtensionFactory,
        ShippingExperienceInterfaceFactory $shippingExperienceFactory,
        OrderRepositoryInterface $orderRepository,
        OrderCollectionPointRepositoryInterface $collectionPointRepository
    ) {
        $this->shippingExtensionFactory = $shippingExtensionFactory;
        $this->shippingExperienceFactory = $shippingExperienceFactory;
        $this->orderRepository = $orderRepository;
        $this->collectionPointRepository = $collectionPointRepository;
    }

    /**
     * For Temando shipments, add the shipping experience information
     * (as selected during checkout) to to the shipment.
     *
     * @param ShippingBuilder $shippingBuilder
     * @param ShippingInterface $shipping
     * @return ShippingInterface
     */
    public function afterCreate(
        ShippingBuilder $shippingBuilder,
        ShippingInterface $shipping = null
    ) {
        if (!$shipping) {
            return $shipping;
        }

        $isTemandoShipping = 0;
        $shippingMethod = $shipping->getMethod();
        $carrierCode = Carrier::CODE;
        $methodCode = preg_replace("/{$carrierCode}_/", '', $shippingMethod, 1, $isTemandoShipping);
        if (!$isTemandoShipping) {
            return $shipping;
        }

        $extensionAttributes = $shipping->getExtensionAttributes();
        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->shippingExtensionFactory->create();
        }

        /** @var \Magento\Sales\Model\Order\Address $orderAddress */
        $orderAddress = $shipping->getAddress();
        $order = $orderAddress->getOrder();
        $shippingExperience = $this->shippingExperienceFactory->create([
            ShippingExperienceInterface::LABEL => $order->getShippingDescription(),
            ShippingExperienceInterface::CODE => $methodCode,
            ShippingExperienceInterface::COST => floatval($order->getBaseShippingInclTax()),
        ]);
        $extensionAttributes->setShippingExperience($shippingExperience);

        try {
            $orderReference = $this->orderRepository->getReferenceByOrderId($order->getId());
            $extensionAttributes->setExtOrderId($orderReference->getExtOrderId());
        } catch (LocalizedException $e) {
            $extensionAttributes->setExtOrderId('');
        }

        try {
            $collectionPoint = $this->collectionPointRepository->get($orderAddress->getEntityId());
        } catch (LocalizedException $e) {
            $collectionPoint = null;
        }

        if ($collectionPoint) {
            $extensionAttributes->setCollectionPoint($collectionPoint);
        }

        $shipping->setExtensionAttributes($extensionAttributes);

        return $shipping;
    }
}
