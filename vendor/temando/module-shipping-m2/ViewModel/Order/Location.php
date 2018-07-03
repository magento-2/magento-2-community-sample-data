<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Order;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Temando\Shipping\Api\Data\CollectionPoint\OrderCollectionPointInterface;
use Temando\Shipping\Model\ResourceModel\Repository\OrderCollectionPointRepositoryInterface;

/**
 * View model for order locations.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class Location implements ArgumentInterface
{
    /**
     * @var OrderCollectionPointRepositoryInterface
     */
    private $collectionPointRepository;

    /**
     * @var OrderAddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * Location constructor.
     * @param OrderCollectionPointRepositoryInterface $collectionPointRepository
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param AddressRenderer $addressRenderer
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        OrderCollectionPointRepositoryInterface $collectionPointRepository,
        OrderAddressInterfaceFactory $addressFactory,
        AddressRenderer $addressRenderer,
        RegionFactory $regionFactory
    ) {
        $this->collectionPointRepository = $collectionPointRepository;
        $this->addressFactory = $addressFactory;
        $this->addressRenderer = $addressRenderer;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param string[] $addressData
     * @return string
     */
    private function getFormattedAddress(array $addressData)
    {
        /** @var \Magento\Sales\Model\Order\Address $address */
        $address = $this->addressFactory->create(['data' => $addressData]);
        $formattedAddress = $this->addressRenderer->format($address, 'html');
        return (string) $formattedAddress;
    }

    /**
     * @param OrderInterface|\Magento\Sales\Model\Order $order
     * @return string
     */
    public function getCollectionPointAddressHtml(OrderInterface $order)
    {
        $shippingAddressId = $order->getShippingAddress()->getId();

        try {
            /** @var OrderCollectionPointInterface $collectionPoint */
            $collectionPoint = $this->collectionPointRepository->get($shippingAddressId);
        } catch (LocalizedException $e) {
            return '';
        }

        try {
            /** @var \Magento\Directory\Model\Region $region */
            $region = $this->regionFactory->create();
            $region = $region->loadByCode($collectionPoint->getRegion(), $collectionPoint->getCountry());
            $regionName = $region->getName();
        } catch (\Exception $e) {
            $regionName = $collectionPoint->getRegion();
        }

        $addressData = [
            'company'    => $collectionPoint->getName(),
            'street'     => $collectionPoint->getStreet(),
            'region'     => $regionName,
            'city'       => $collectionPoint->getCity(),
            'postcode'   => $collectionPoint->getPostcode(),
            'country_id' => $collectionPoint->getCountry(),

        ];

        return $this->getFormattedAddress($addressData);
    }
}
