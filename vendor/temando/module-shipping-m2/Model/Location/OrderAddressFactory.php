<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Location;

use Magento\Directory\Model\RegionFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Model\Order\Address;
use Temando\Shipping\Api\Data\Delivery\OrderCollectionPointInterface;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterface;
use Temando\Shipping\Model\Shipment\LocationInterface;

/**
 * Temando Order Address Factory
 *
 * Create order address from different location entities
 *
 * @package Temando\Shipping\Model
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderAddressFactory
{
    /**
     * @var OrderAddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * OrderAddressFactory constructor.
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param RegionFactory $regionFactory
     */
    public function __construct(OrderAddressInterfaceFactory $addressFactory, RegionFactory $regionFactory)
    {
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param string[] $addressData
     * @return OrderAddressInterface
     */
    private function create(array $addressData)
    {
        try {
            /** @var \Magento\Directory\Model\Region $region */
            $region     = $this->regionFactory->create();
            $region     = $region->loadByCode($addressData['region'], $addressData['country_id']);
            $regionName = $region->getName() ?: $addressData['region'];

            $addressData['region'] = $regionName;
            $address = $this->addressFactory->create(['data' => $addressData]);
        } catch (\Exception $e) {
            $address = $this->addressFactory->create(['data' => $addressData]);
        }

        $address->setAddressType(Address::TYPE_SHIPPING);
        return $address;
    }

    /**
     * @param LocationInterface $location
     * @return OrderAddressInterface
     */
    public function createFromShipmentLocation(LocationInterface $location)
    {
        $addressData = [
            'firstname'  => $location->getPersonFirstName(),
            'lastname'   => $location->getPersonLastName(),
            'company'    => $location->getCompany(),
            'street'     => $location->getStreet(),
            'city'       => $location->getCity(),
            'postcode'   => $location->getPostalCode(),
            'region'     => $location->getRegionCode(),
            'country_id' => $location->getCountryCode(),
            'email'      => $location->getEmail(),
            'telephone'  => $location->getPhoneNumber(),
        ];

        return $this->create($addressData);
    }

    /**
     * @param OrderPickupLocationInterface $location
     * @return OrderAddressInterface
     */
    public function createFromPickupLocation(OrderPickupLocationInterface $location)
    {
        $addressData = [
            'company'    => $location->getName(),
            'street'     => $location->getStreet(),
            'city'       => $location->getCity(),
            'postcode'   => $location->getPostcode(),
            'region'     => $location->getRegion(),
            'country_id' => $location->getCountry(),
        ];

        return $this->create($addressData);
    }

    /**
     * @param OrderCollectionPointInterface $location
     * @return OrderAddressInterface
     */
    public function createFromCollectionPoint(OrderCollectionPointInterface $location)
    {
        $addressData = [
            'company'    => $location->getName(),
            'street'     => $location->getStreet(),
            'city'       => $location->getCity(),
            'postcode'   => $location->getPostcode(),
            'region'     => $location->getRegion(),
            'country_id' => $location->getCountry(),
        ];

        return $this->create($addressData);
    }
}
