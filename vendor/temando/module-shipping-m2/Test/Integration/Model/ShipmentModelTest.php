<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Model\Shipment\FulfillmentInterface;
use Temando\Shipping\Model\Shipment\LocationInterface;

/**
 * Temando Shipment Model Test
 *
 * @codingStandardsIgnoreFile
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $shipmentId = '00000000-5000-0005-0000-000000000000';
        $originLocationData = [
            'company' => 'Foo Ltd.',
            'person_first_name' => 'Foo',
            'person_last_name' => 'Bar',
            'email' => 'foo@example.org',
            'phone_number' => '0800',
            'street' => 'Foo Street',
            'city' => 'South Foo',
            'postal_code' => 'F00 84R',
            'region_code' => 'YY',
            'country_code' => 'XXX',
        ];
        $destinationLocationData = [
            'company' => 'Fox, Inc.',
            'person_first_name' => 'Fox',
            'person_last_name' => 'Baz',
            'email' => 'fox@example.org',
            'phone_number' => '911',
            'street' => 'Fox Street',
            'city' => 'North Fox',
            'postal_code' => 'F0X 842',
            'region_code' => 'AA',
            'country_code' => 'BBB',
        ];
        $fulfillmentData = [
            'service_name' => 'Faster',
            'tracking_reference' => 'ZZ1234',
        ];
        $documentationData = [
            'documentation_id' => '1234',
            'description' => 'Package Label',
            'type' => 'packageLabels',
            'size' => 'A6',
            'mime_type' => 'image/png',
            'url' => 'https://example.com/documents/label-1234',
        ];
        $isPaperless = true;

        /** @var Shipment\Location $originLocation */
        $originLocation = Bootstrap::getObjectManager()->create(Shipment\Location::class, ['data' => [
            LocationInterface::COMPANY => $originLocationData['company'],
            LocationInterface::PERSON_FIRST_NAME => $originLocationData['person_first_name'],
            LocationInterface::PERSON_LAST_NAME => $originLocationData['person_last_name'],
            LocationInterface::EMAIL => $originLocationData['email'],
            LocationInterface::PHONE_NUMBER => $originLocationData['phone_number'],
            LocationInterface::STREET => $originLocationData['street'],
            LocationInterface::CITY => $originLocationData['city'],
            LocationInterface::POSTAL_CODE => $originLocationData['postal_code'],
            LocationInterface::REGION_CODE => $originLocationData['region_code'],
            LocationInterface::COUNTRY_CODE => $originLocationData['country_code'],
        ]]);

        $destinationLocation = Bootstrap::getObjectManager()->create(Shipment\Location::class, ['data' => [
            LocationInterface::COMPANY => $destinationLocationData['company'],
            LocationInterface::PERSON_FIRST_NAME => $destinationLocationData['person_first_name'],
            LocationInterface::PERSON_LAST_NAME => $destinationLocationData['person_last_name'],
            LocationInterface::EMAIL => $destinationLocationData['email'],
            LocationInterface::PHONE_NUMBER => $destinationLocationData['phone_number'],
            LocationInterface::STREET => $destinationLocationData['street'],
            LocationInterface::CITY => $destinationLocationData['city'],
            LocationInterface::POSTAL_CODE => $destinationLocationData['postal_code'],
            LocationInterface::REGION_CODE => $destinationLocationData['region_code'],
            LocationInterface::COUNTRY_CODE => $destinationLocationData['country_code'],
        ]]);

        $fulfillment = Bootstrap::getObjectManager()->create(Shipment\Fulfillment::class, ['data' => [
            FulfillmentInterface::SERVICE_NAME => $fulfillmentData['service_name'],
            FulfillmentInterface::TRACKING_REFERENCE => $fulfillmentData['tracking_reference'],
        ]]);

        /** @var Documentation $documentation */
        $documentation = Bootstrap::getObjectManager()->create(Documentation::class, ['data' => [
            DocumentationInterface::DOCUMENTATION_ID => $documentationData['documentation_id'],
            DocumentationInterface::NAME => $documentationData['description'],
            DocumentationInterface::TYPE => $documentationData['type'],
            DocumentationInterface::SIZE => $documentationData['size'],
            DocumentationInterface::MIME_TYPE => $documentationData['mime_type'],
            DocumentationInterface::URL => $documentationData['url'],
        ]]);
        $documentation = [$documentation];

        /** @var Shipment $shipment */
        $shipment = Bootstrap::getObjectManager()->create(Shipment::class, ['data' => [
            ShipmentInterface::SHIPMENT_ID => $shipmentId,
            ShipmentInterface::ORIGIN_LOCATION => $originLocation,
            ShipmentInterface::DESTINATION_LOCATION => $destinationLocation,
            ShipmentInterface::FULFILLMENT => $fulfillment,
            ShipmentInterface::DOCUMENTATION => $documentation,
            ShipmentInterface::IS_PAPERLESS => $isPaperless,
        ]]);

        $this->assertEquals($shipmentId, $shipment->getShipmentId());

        $this->assertSame($originLocation, $shipment->getOriginLocation());
        $this->assertEquals($originLocationData['company'], $shipment->getOriginLocation()->getCompany());
        $this->assertEquals($originLocationData['person_first_name'], $shipment->getOriginLocation()->getPersonFirstName());
        $this->assertEquals($originLocationData['person_last_name'], $shipment->getOriginLocation()->getPersonLastName());
        $this->assertEquals($originLocationData['email'], $shipment->getOriginLocation()->getEmail());
        $this->assertEquals($originLocationData['phone_number'], $shipment->getOriginLocation()->getPhoneNumber());
        $this->assertEquals($originLocationData['street'], $shipment->getOriginLocation()->getStreet());
        $this->assertEquals($originLocationData['city'], $shipment->getOriginLocation()->getCity());
        $this->assertEquals($originLocationData['postal_code'], $shipment->getOriginLocation()->getPostalCode());
        $this->assertEquals($originLocationData['region_code'], $shipment->getOriginLocation()->getRegionCode());
        $this->assertEquals($originLocationData['country_code'], $shipment->getOriginLocation()->getCountryCode());

        $this->assertSame($destinationLocation, $shipment->getDestinationLocation());
        $this->assertEquals($destinationLocationData['company'], $shipment->getDestinationLocation()->getCompany());
        $this->assertEquals($destinationLocationData['person_first_name'], $shipment->getDestinationLocation()->getPersonFirstName());
        $this->assertEquals($destinationLocationData['person_last_name'], $shipment->getDestinationLocation()->getPersonLastName());
        $this->assertEquals($destinationLocationData['email'], $shipment->getDestinationLocation()->getEmail());
        $this->assertEquals($destinationLocationData['phone_number'], $shipment->getDestinationLocation()->getPhoneNumber());
        $this->assertEquals($destinationLocationData['street'], $shipment->getDestinationLocation()->getStreet());
        $this->assertEquals($destinationLocationData['city'], $shipment->getDestinationLocation()->getCity());
        $this->assertEquals($destinationLocationData['postal_code'], $shipment->getDestinationLocation()->getPostalCode());
        $this->assertEquals($destinationLocationData['region_code'], $shipment->getDestinationLocation()->getRegionCode());
        $this->assertEquals($destinationLocationData['country_code'], $shipment->getDestinationLocation()->getCountryCode());

        $this->assertSame($fulfillment, $shipment->getFulfillment());
        $this->assertEquals($fulfillmentData['service_name'], $shipment->getFulfillment()->getServiceName());
        $this->assertEquals($fulfillmentData['tracking_reference'], $shipment->getFulfillment()->getTrackingReference());

        $this->assertSame($documentation, $shipment->getDocumentation());

        $this->assertEquals($isPaperless, $shipment->isPaperless());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $shipmentId = '00000000-5000-0005-0000-000000000000';
        $orderId = '1234567899876543210';
        $originId = '897654321123456789';
        /** @var Shipment\Location $originLocation */
        $originLocation = Bootstrap::getObjectManager()->create(Shipment\Location::class);
        /** @var Shipment\Location $destinationLocation */
        $destinationLocation = Bootstrap::getObjectManager()->create(Shipment\Location::class);
        $fulfillment = Bootstrap::getObjectManager()->create(FulfillmentInterface::class);
        $packages = ['pack'];
        $documentation = ['doc'];
        $isPaperless = true;

        /** @var Shipment $shipment */
        $shipment = Bootstrap::getObjectManager()->create(Shipment::class);

        $this->assertEmpty($shipment->getShipmentId());

        $shipment->setData(Shipment::SHIPMENT_ID, $shipmentId);
        $this->assertEquals($shipmentId, $shipment->getShipmentId());

        $shipment->setData(Shipment::ORDER_ID, $orderId);
        $this->assertEquals($orderId, $shipment->getOrderId());

        $shipment->setData(Shipment::ORIGIN_LOCATION, $originLocation);
        $this->assertSame($originLocation, $shipment->getOriginLocation());

        $shipment->setData(Shipment::DESTINATION_LOCATION, $destinationLocation);
        $this->assertSame($destinationLocation, $shipment->getDestinationLocation());

        $shipment->setData(Shipment::ORIGIN_ID, $originId);
        $this->assertSame($originId, $shipment->getOriginId());

        $shipment->setData(Shipment::FULFILLMENT, $fulfillment);
        $this->assertEquals($fulfillment, $shipment->getFulfillment());

        $shipment->setData(Shipment::PACKAGES, $packages);
        $this->assertEquals($packages, $shipment->getPackages());

        $shipment->setData(Shipment::DOCUMENTATION, $documentation);
        $this->assertEquals($documentation, $shipment->getDocumentation());

        $shipment->setData(Shipment::IS_PAPERLESS, $isPaperless);
        $this->assertEquals($isPaperless, $shipment->isPaperless());
    }
}
