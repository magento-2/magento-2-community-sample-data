<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Generic;

/**
 * Temando API Location Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Location
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Location\Address
     */
    private $address;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Location\Contact
     */
    private $contact;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Location\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Location\Address $address
     * @return void
     */
    public function setAddress(\Temando\Shipping\Rest\Response\Type\Generic\Location\Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Location\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Location\Contact $contact
     * @return void
     */
    public function setContact(\Temando\Shipping\Rest\Response\Type\Generic\Location\Contact $contact)
    {
        $this->contact = $contact;
    }
}
