<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Shipment;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class ShipmentOriginTest extends TestCase
{
    /** @var ObjectManager $objectManager */
    private $objectManager;
    /** @var Location $shipmentOrigin */
    private $shipmentOrigin;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->shipmentOrigin = $this->objectManager->create(Location::class);
        $this->shipmentOrigin->setData(LocationInterface::CITY, 'CITY');
        $this->shipmentOrigin->setData(LocationInterface::COMPANY, 'COMPANY');
        $this->shipmentOrigin->setData(LocationInterface::COUNTRY_CODE, 'COUNTRY_CODE');
        $this->shipmentOrigin->setData(LocationInterface::EMAIL, 'EMAIL');
        $this->shipmentOrigin->setData(LocationInterface::PERSON_FIRST_NAME, 'PERSON_FIRST_NAME');
        $this->shipmentOrigin->setData(LocationInterface::PERSON_LAST_NAME, 'PERSON_LAST_NAME');
        $this->shipmentOrigin->setData(LocationInterface::PHONE_NUMBER, 'PHONE_NUMBER');
        $this->shipmentOrigin->setData(LocationInterface::POSTAL_CODE, 'POSTAL_CODE');
        $this->shipmentOrigin->setData(LocationInterface::REGION_CODE, 'REGION_CODE');
        $this->shipmentOrigin->setData(LocationInterface::STREET, 'STREET');
    }

    /**
     * @test
     */
    public function getCompanyTest()
    {
        $result = $this->shipmentOrigin->getCompany();
        $this->assertEquals($result, "COMPANY");
    }

    /**
     * @test
     */
    public function getPersonFirstNameTest()
    {
        $result = $this->shipmentOrigin->getPersonFirstName();
        $this->assertEquals($result, "PERSON_FIRST_NAME");
    }

    /**
     * @test
     */
    public function getPersonLastNameTest()
    {
        $result = $this->shipmentOrigin->getPersonLastName();
        $this->assertEquals($result, "PERSON_LAST_NAME");
    }

    /**
     * @test
     */
    public function getEmailTest()
    {
        $result = $this->shipmentOrigin->getEmail();
        $this->assertEquals($result, "EMAIL");
    }

    /**
     * @test
     */
    public function getPhoneNumberTest()
    {
        $result = $this->shipmentOrigin->getPhoneNumber();
        $this->assertEquals($result, "PHONE_NUMBER");
    }

    /**
     * @test
     */
    public function getStreetTest()
    {
        $result = $this->shipmentOrigin->getStreet();
        $this->assertEquals($result, "STREET");
    }

    /**
     * @test
     */
    public function getCityTest()
    {
        $result = $this->shipmentOrigin->getCity();
        $this->assertEquals($result, "CITY");
    }

    /**
     * @test
     */
    public function getPostalCodeTest()
    {
        $result = $this->shipmentOrigin->getPostalCode();
        $this->assertEquals($result, "POSTAL_CODE");
    }

    /**
     * @test
     */
    public function getRegionCodeTest()
    {
        $result = $this->shipmentOrigin->getRegionCode();
        $this->assertEquals($result, "REGION_CODE");
    }

    /**
     * @test
     */
    public function getCountryCodeTest()
    {
        $result = $this->shipmentOrigin->getCountryCode();
        $this->assertEquals($result, "COUNTRY_CODE");
    }
}
