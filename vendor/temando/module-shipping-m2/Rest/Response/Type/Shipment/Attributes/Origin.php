<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Shipment\Attributes;

/**
 * Temando API Shipment Origin Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Origin
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Address
     */
    private $address;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Contact
     */
    private $contact;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Address $address
     * @return void
     */
    public function setAddress(\Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Contact $contact
     * @return void
     */
    public function setContact(\Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Origin\Contact $contact)
    {
        $this->contact = $contact;
    }
}
