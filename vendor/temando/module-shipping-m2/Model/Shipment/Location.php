<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\DataObject;

/**
 * Temando Shipment Location Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Location extends DataObject implements LocationInterface
{
    /**
     * Get location name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(LocationInterface::NAME);
    }

    /**
     * Get organisation name.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->getData(LocationInterface::COMPANY);
    }

    /**
     * Get contact person's first name.
     *
     * @return string
     */
    public function getPersonFirstName()
    {
        return $this->getData(LocationInterface::PERSON_FIRST_NAME);
    }

    /**
     * Get contact person's last name.
     *
     * @return string
     */
    public function getPersonLastName()
    {
        return $this->getData(LocationInterface::PERSON_LAST_NAME);
    }

    /**
     * Get contact person's email address.
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(LocationInterface::EMAIL);
    }

    /**
     * Get contact person's telephone number.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->getData(LocationInterface::PHONE_NUMBER);
    }

    /**
     * Get address street lines.
     *
     * @return string[]
     */
    public function getStreet()
    {
        return $this->getData(LocationInterface::STREET);
    }

    /**
     * Get address locality.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getData(LocationInterface::CITY);
    }

    /**
     * Get address postal code.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->getData(LocationInterface::POSTAL_CODE);
    }

    /**
     * Get address administrative area.
     *
     * @return string
     */
    public function getRegionCode()
    {
        return $this->getData(LocationInterface::REGION_CODE);
    }

    /**
     * Get address country ISO 2 code.
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getData(LocationInterface::COUNTRY_CODE);
    }

    /**
     * Get address type, e.g. "Store", "Warehouse", etc.
     * @return string
     */
    public function getType()
    {
        return $this->getData(LocationInterface::TYPE);
    }

    /**
     * Get location opening hours
     *
     * @return string[][]
     */
    public function getOpeningHours()
    {
        return $this->getData(LocationInterface::OPENING_HOURS);
    }
}
