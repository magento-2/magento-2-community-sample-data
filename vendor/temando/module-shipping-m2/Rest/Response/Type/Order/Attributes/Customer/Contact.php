<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Order\Attributes\Customer;

/**
 * Temando API Order Attributes Customer Contact Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Contact
{
    /**
     * @var string
     */
    private $organisationName;

    /**
     * @var string
     */
    private $personFirstName;

    /**
     * @var string
     */
    private $personLastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string[]
     */
    private $phoneNumbers = [];

    /**
     * @var string
     */
    private $faxNumber;

    /**
     * @var string
     */
    private $nationalIdentificationNumber;

    /**
     * @var string
     */
    private $taxIdentificationNumber;

    /**
     * @return string
     */
    public function getOrganisationName()
    {
        return $this->organisationName;
    }

    /**
     * @param string $organisationName
     * @return void
     */
    public function setOrganisationName($organisationName)
    {
        $this->organisationName = $organisationName;
    }

    /**
     * @return string
     */
    public function getPersonFirstName()
    {
        return $this->personFirstName;
    }

    /**
     * @param string $personFirstName
     * @return void
     */
    public function setPersonFirstName($personFirstName)
    {
        $this->personFirstName = $personFirstName;
    }

    /**
     * @return string
     */
    public function getPersonLastName()
    {
        return $this->personLastName;
    }

    /**
     * @param string $personLastName
     * @return void
     */
    public function setPersonLastName($personLastName)
    {
        $this->personLastName = $personLastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string[]
     */
    public function getPhoneNumbers()
    {
        return $this->phoneNumbers;
    }

    /**
     * @param string[] $phoneNumbers
     * @return void
     */
    public function setPhoneNumbers(array $phoneNumbers)
    {
        $this->phoneNumbers = $phoneNumbers;
    }

    /**
     * @return string
     */
    public function getFaxNumber()
    {
        return $this->faxNumber;
    }

    /**
     * @param string $faxNumber
     * @return void
     */
    public function setFaxNumber($faxNumber)
    {
        $this->faxNumber = $faxNumber;
    }

    /**
     * @return string
     */
    public function getNationalIdentificationNumber()
    {
        return $this->nationalIdentificationNumber;
    }

    /**
     * @param string $nationalIdentificationNumber
     * @return void
     */
    public function setNationalIdentificationNumber($nationalIdentificationNumber)
    {
        $this->nationalIdentificationNumber = $nationalIdentificationNumber;
    }

    /**
     * @return string
     */
    public function getTaxIdentificationNumber()
    {
        return $this->taxIdentificationNumber;
    }

    /**
     * @param string $taxIdentificationNumber
     * @return void
     */
    public function setTaxIdentificationNumber($taxIdentificationNumber)
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;
    }
}
