<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Shipment\ExportDeclaration;

/**
 * Temando API Shipment Export Declaration Signatory Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Signatory
{
    /**
     * @var string
     */
    private $personFirstName;

    /**
     * @var string
     */
    private $personTitle;

    /**
     * @var string
     */
    private $personLastName;

    /**
     * @return string
     */
    public function getPersonFirstName()
    {
        return $this->personFirstName;
    }

    /**
     * @param string $personFirstName
     */
    public function setPersonFirstName($personFirstName)
    {
        $this->personFirstName = $personFirstName;
    }

    /**
     * @return string
     */
    public function getPersonTitle()
    {
        return $this->personTitle;
    }

    /**
     * @param string $personTitle
     */
    public function setPersonTitle($personTitle)
    {
        $this->personTitle = $personTitle;
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
     */
    public function setPersonLastName($personLastName)
    {
        $this->personLastName = $personLastName;
    }
}
