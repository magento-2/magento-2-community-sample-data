<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Location;

use Temando\Shipping\Rest\Response\Type\Location\Attributes\Address;
use Temando\Shipping\Rest\Response\Type\Location\Attributes\Contact;
use Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata;

/**
 * Temando API Location Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Attributes
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Location\Attributes\Address
     */
    private $address;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Location\Attributes\Contact
     */
    private $contact;

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata
     */
    private $geodata;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     * @return void
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Location\Attributes\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Location\Attributes\Address $address
     * @return void
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Location\Attributes\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Location\Attributes\Contact $contact
     * @return void
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @param string $uniqueId
     * @return void
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return void
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata
     */
    public function getGeodata()
    {
        return $this->geodata;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Location\Attributes\Geodata $geodata
     * @return void
     */
    public function setGeodata(Geodata $geodata)
    {
        $this->geodata = $geodata;
    }
}
