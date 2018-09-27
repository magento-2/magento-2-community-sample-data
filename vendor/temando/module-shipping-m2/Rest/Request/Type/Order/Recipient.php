<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type\Order;

use Temando\Shipping\Rest\Request\Type\AttributeFilter;
use Temando\Shipping\Rest\Request\Type\EmptyFilterableInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeAttribute;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeProcessor;

/**
 * Temando recipient entity (consignee, deliver-to)
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Recipient implements ExtensibleTypeInterface, EmptyFilterableInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private $organisationName;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $faxNumber;

    /**
     * @var string
     */
    private $nationalId;

    /**
     * @var string
     */
    private $taxId;

    /**
     * @var string[]
     */
    private $street;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $administrativeArea;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $dependentLocality = '';

    /**
     * @var float
     */
    private $longitude = null;

    /**
     * @var float
     */
    private $latitude = null;

    /**
     * @var string
     */
    private $collectionPointId;

    /**
     * @var ExtensibleTypeAttribute[]
     */
    private $additionalAttributes = [];

    /**
     * Recipient constructor.
     * @param string $organisationName
     * @param string $lastname
     * @param string $firstname
     * @param string $email
     * @param string $phoneNumber
     * @param string $faxNumber
     * @param string $nationalId
     * @param string $taxId
     * @param string[] $street
     * @param string $countryCode
     * @param string $administrativeArea
     * @param string $postalCode
     * @param string $locality
     * @param string $dependentLocality
     * @param float $longitude
     * @param float $latitude
     * @param string $collectionPointId
     */
    public function __construct(
        $organisationName,
        $lastname,
        $firstname,
        $email,
        $phoneNumber,
        $faxNumber,
        $nationalId,
        $taxId,
        array $street,
        $countryCode,
        $administrativeArea,
        $postalCode,
        $locality,
        $dependentLocality = '',
        $longitude = null,
        $latitude = null,
        $collectionPointId = ''
    ) {
        $this->organisationName = $organisationName;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->faxNumber = $faxNumber;
        $this->nationalId = $nationalId;
        $this->taxId = $taxId;
        $this->street = $street;
        $this->countryCode = $countryCode;
        $this->administrativeArea = $administrativeArea;
        $this->postalCode = $postalCode;
        $this->locality = $locality;
        $this->dependentLocality = $dependentLocality;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->collectionPointId = $collectionPointId;
    }

    /**
     * Add further dynamic request attributes to the request type.
     *
     * @param ExtensibleTypeAttribute $attribute
     * @return void
     */
    public function addAdditionalAttribute(ExtensibleTypeAttribute $attribute)
    {
        $this->additionalAttributes[$attribute->getAttributeId()] = $attribute;
    }

    /**
     * @return mixed[]|string[][]
     */
    public function jsonSerialize()
    {
        $recipient = [
            'contact' => [
                'organisationName' => $this->organisationName,
                'personFirstName' => $this->firstname,
                'personLastName' => $this->lastname,
                'email' => $this->email,
                'phoneNumber' => $this->phoneNumber,
                'faxNumber' => $this->faxNumber,
                'nationalIdentificationNumber' => $this->nationalId,
                'taxIdentificationNumber' => $this->taxId,
            ],
            'address' => [
                'lines' => $this->street,
                'countryCode' => $this->countryCode,
                'postalCode' => $this->postalCode,
                'locality' => $this->locality,
                'dependentLocality' => $this->dependentLocality,
                'administrativeArea' => $this->administrativeArea,
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
            ],
            'options' => [],
            'customAttributes' => [],
        ];

        if ($this->collectionPointId) {
            $recipient['options']['collectionPoint'] = true;
            $recipient['customAttributes']['collectionPointId'] = $this->collectionPointId;
        } else {
            // add dynamic request attributes only for regular shipping addresses
            foreach ($this->additionalAttributes as $additionalAttribute) {
                $recipient = ExtensibleTypeProcessor::addAttribute($recipient, $additionalAttribute);
            }
        }

        $recipient = AttributeFilter::notEmpty($recipient);

        return $recipient;
    }

    /**
     * Check if any properties are set.
     *
     * @return bool
     */
    public function isEmpty()
    {
        $properties = get_object_vars($this);
        $properties = AttributeFilter::notEmpty($properties);
        return empty($properties);
    }
}
