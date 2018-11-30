<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

use Temando\Shipping\Rest\Response\Fields\Location\Address;
use Temando\Shipping\Rest\Response\Fields\Location\Contact;
use Temando\Shipping\Rest\Response\Fields\Location\Geodata;

/**
 * Temando API Location Resource Object Attributes
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class LocationAttributes
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\Address
     */
    private $address;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\Contact
     */
    private $contact;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours
     */
    private $openingHours;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * @var bool
     */
    private $isClickAndCollect;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $modifiedAt;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Location\Geodata
     */
    private $geodata;

    /**
     * @var string[]
     */
    private $customAttributes;

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
     * @return \Temando\Shipping\Rest\Response\Fields\Location\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\Address $address
     * @return void
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Location\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\Contact $contact
     * @return void
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours
     */
    public function getOpeningHours()
    {
        return $this->openingHours;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\OpeningHours $openingHours
     * @return void
     */
    public function setOpeningHours($openingHours)
    {
        $this->openingHours = $openingHours;
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
        $this->isDefault = (bool)$isDefault;
    }

    /**
     * @return bool
     */
    public function getIsClickAndCollect()
    {
        return $this->isClickAndCollect;
    }

    /**
     * @param bool $isClickAndCollect
     */
    public function setIsClickAndCollect($isClickAndCollect)
    {
        $this->isClickAndCollect = (bool)$isClickAndCollect;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     * @return void
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
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
     * @return \Temando\Shipping\Rest\Response\Fields\Location\Geodata
     */
    public function getGeodata()
    {
        return $this->geodata;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Location\Geodata $geodata
     * @return void
     */
    public function setGeodata(Geodata $geodata)
    {
        $this->geodata = $geodata;
    }

    /**
     * @return string[]
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @param string[] $customAttributes
     * @return void
     */
    public function setCustomAttributes(array $customAttributes)
    {
        $this->customAttributes = $customAttributes;
    }
}
