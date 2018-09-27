<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Framework\DataObject;

/**
 * Temando Order Billing Entity
 *
 * An order billing address as associated with an order entity at the Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderBilling extends DataObject implements OrderBillingInterface
{
    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->getData(OrderBillingInterface::COMPANY);
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->getData(OrderBillingInterface::LASTNAME);
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->getData(OrderBillingInterface::FIRSTNAME);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(OrderBillingInterface::EMAIL);
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->getData(OrderBillingInterface::PHONE);
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->getData(OrderBillingInterface::FAX);
    }

    /**
     * @return string
     */
    public function getNationalId()
    {
        return $this->getData(OrderBillingInterface::NATIONAL_ID);
    }

    /**
     * @return string
     */
    public function getTaxId()
    {
        return $this->getData(OrderBillingInterface::TAX_ID);
    }

    /**
     * @return string[]
     */
    public function getStreet()
    {
        return $this->getData(OrderBillingInterface::STREET);
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getData(OrderBillingInterface::COUNTRY_CODE);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(OrderBillingInterface::REGION);
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->getData(OrderBillingInterface::POSTAL_CODE);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getData(OrderBillingInterface::CITY);
    }

    /**
     * @return string
     */
    public function getSuburb()
    {
        return $this->getData(OrderBillingInterface::SUBURB);
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->getData(OrderBillingInterface::LONGITUDE);
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->getData(OrderBillingInterface::LATITUDE);
    }
}
