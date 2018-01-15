<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Framework\DataObject;

/**
 * Temando Order Recipient Interface
 *
 * An order recipient as associated with an order entity at the Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderRecipient extends DataObject implements OrderRecipientInterface
{
    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->getData(OrderRecipientInterface::COMPANY);
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->getData(OrderRecipientInterface::LASTNAME);
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->getData(OrderRecipientInterface::FIRSTNAME);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(OrderRecipientInterface::EMAIL);
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->getData(OrderRecipientInterface::PHONE);
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->getData(OrderRecipientInterface::FAX);
    }

    /**
     * @return string
     */
    public function getNationalId()
    {
        return $this->getData(OrderRecipientInterface::NATIONAL_ID);
    }

    /**
     * @return string
     */
    public function getTaxId()
    {
        return $this->getData(OrderRecipientInterface::TAX_ID);
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->getData(OrderRecipientInterface::STREET);
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getData(OrderRecipientInterface::COUNTRY_CODE);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(OrderRecipientInterface::REGION);
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->getData(OrderRecipientInterface::POSTAL_CODE);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData(OrderRecipientInterface::CITY);
    }

    /**
     * @return string
     */
    public function getSuburb()
    {
        return $this->getData(OrderRecipientInterface::SUBURB);
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->getData(OrderRecipientInterface::LONGITUDE);
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->getData(OrderRecipientInterface::LATITUDE);
    }
}
