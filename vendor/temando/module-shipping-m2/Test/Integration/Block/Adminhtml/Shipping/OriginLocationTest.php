<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\Configuration;

use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Block\Adminhtml\Shipping\View\OriginLocation;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * Temando View Shipment Page Origin Location Section Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OriginLocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * assert origin location block is not being displayed when there is no shipment
     *
     * @test
     */
    public function apiShipmentIsNotRegistered()
    {
        $addressFactoryMock = $this->getMockBuilder(OrderAddressInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $addressFactoryMock
            ->expects($this->never())
            ->method('create');

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var OriginLocation $block */
        $block = $layout->createBlock(OriginLocation::class, '', ['addressFactory' => $addressFactoryMock]);

        $address = $block->getFormattedAddress();
        $this->assertEmpty($address);
    }

    /**
     * assert origin location block is not being displayed when there is no shipment
     *
     * @test
     */
    public function apiShipmentRegistered()
    {
        $region = 'foo region';
        $postcode = '303';
        $lastname = 'foo lastname';
        $street = 'foo street 42';
        $city = 'foo city';
        $email = 'foo@bar.com';
        $telephone = '0061';
        $country_id = 'AU';
        $country = 'Australia';
        $firstname = 'foo firstname';
        $company = 'foo company';

        $originLocation = new DataObject([
            'region_code' => $region,
            'postal_code' => $postcode,
            'person_last_name' => $lastname,
            'street' => $street,
            'city' => $city,
            'email' => $email,
            'phone_number' => $telephone,
            'country_code' => $country_id,
            'person_first_name' => $firstname,
            'company' => $company
        ]);

        $shipment = new DataObject(['origin_location' => $originLocation]);

        $providerMock = $this->getMockBuilder(ShipmentProviderInterface::class)
                             ->disableOriginalConstructor()
                             ->setMethods(['getShipment', 'setShipment', 'getSalesShipment', 'setSalesShipment'])
                             ->getMock();
        $providerMock
            ->expects($this->once())
            ->method('getShipment')
            ->willReturn($shipment);

        /** @var LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        /** @var OriginLocation $block */
        $block = $layout->createBlock(OriginLocation::class, '', ['shipmentProvider' => $providerMock]);

        $address = $block->getFormattedAddress();
        $this->assertContains($region, $address);
        $this->assertContains($postcode, $address);
        $this->assertContains($lastname, $address);
        $this->assertContains($street, $address);
        $this->assertContains($city, $address);
        // default html formatter template does not include email
        $this->assertNotContains($email, $address);
        $this->assertContains($telephone, $address);
        // country name is rendered, not code
        $this->assertNotContains($country_id, $address);
        $this->assertContains($country, $address);
        $this->assertContains($firstname, $address);
        $this->assertContains($company, $address);
    }
}
