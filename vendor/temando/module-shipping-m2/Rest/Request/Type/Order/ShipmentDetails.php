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
 * Temando API Order Shipment Details
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShipmentDetails implements ExtensibleTypeInterface, EmptyFilterableInterface, \JsonSerializable
{
    /**
     * @var Recipient
     */
    private $finalRecipient;

    /**
     * @var string
     */
    private $collectionPointId;

    /**
     * @var ExtensibleTypeAttribute[]
     */
    private $additionalAttributes = [];

    /**
     * ShipmentDetails constructor.
     * @param Recipient $finalRecipient
     * @param string $collectionPointId
     */
    public function __construct(Recipient $finalRecipient, $collectionPointId)
    {
        $this->finalRecipient = $finalRecipient;
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
     * @return mixed[]
     */
    public function jsonSerialize()
    {
        $shipmentDetails = [
            'finalRecipient' => $this->finalRecipient,
            'capabilities' => [],
        ];

        if ($this->collectionPointId) {
            $shipmentDetails['capabilities']['collectionPoints'] = ['required' => true];
        }

        foreach ($this->additionalAttributes as $additionalAttribute) {
            $shipmentDetails = ExtensibleTypeProcessor::addAttribute($shipmentDetails, $additionalAttribute);
        }
        $shipmentDetails = AttributeFilter::notEmpty($shipmentDetails);

        return $shipmentDetails;
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
