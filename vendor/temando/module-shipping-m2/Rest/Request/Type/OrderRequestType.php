<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type;

use Temando\Shipping\Rest\Request\Type\Generic\MonetaryValue;
use Temando\Shipping\Rest\Request\Type\Order\CollectionPointSearch;
use Temando\Shipping\Rest\Request\Type\Order\Customer;
use Temando\Shipping\Rest\Request\Type\Order\Experience;
use Temando\Shipping\Rest\Request\Type\Order\OrderItem;
use Temando\Shipping\Rest\Request\Type\Order\Recipient;
use Temando\Shipping\Rest\Request\Type\Order\ShipmentDetails;

/**
 * Temando API Order
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderRequestType implements OrderRequestTypeInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $lastModifiedAt;

    /**
     * @var string
     */
    private $orderedAt;

    /**
     * @var string
     */
    private $sourceName;

    /**
     * @var string
     */
    private $sourceReference;

    /**
     * Billing address
     *
     * @var Customer
     */
    private $customer;

    /**
     * Shipping address
     *
     * @var Recipient
     */
    private $recipient;

    /**
     * @var OrderItem[]
     */
    private $items;

    /**
     * @var MonetaryValue
     */
    private $total;

    /**
     * @var string[]
     */
    private $aliases;

    /**
     * @var ExtensibleTypeAttribute[]
     */
    private $additionalAttributes = [];

    /**
     * @var Experience
     */
    private $selectedExperience;

    /**
     * @var CollectionPointSearch
     */
    private $collectionPointSearch;

    /**
     * @var ShipmentDetails
     */
    private $shipmentDetails;

    /**
     * OrderRequestType constructor.
     * @param string $id
     * @param string $createdAt
     * @param string $lastModifiedAt
     * @param string $orderedAt
     * @param string $sourceName
     * @param string $sourceReference
     * @param MonetaryValue $total
     * @param Customer $customer
     * @param Recipient $recipient
     * @param OrderItem[] $items
     * @param string[] $aliases
     * @param Experience $selectedExperience
     * @param CollectionPointSearch $collectionPointSearch
     * @param ShipmentDetails $shipmentDetails
     */
    public function __construct(
        $id,
        $createdAt,
        $lastModifiedAt,
        $orderedAt,
        $sourceName,
        $sourceReference,
        MonetaryValue $total,
        Customer $customer,
        Recipient $recipient,
        array $items,
        array $aliases = [],
        Experience $selectedExperience = null,
        CollectionPointSearch $collectionPointSearch = null,
        ShipmentDetails $shipmentDetails = null
    ) {
        $this->type = 'order';
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->lastModifiedAt = $lastModifiedAt;
        $this->orderedAt = $orderedAt;
        $this->sourceName = $sourceName;
        $this->sourceReference = $sourceReference;
        $this->total = $total;
        $this->customer = $customer;
        $this->recipient = $recipient;
        $this->items = $items;
        $this->aliases = $aliases;
        $this->selectedExperience = $selectedExperience;
        $this->collectionPointSearch = $collectionPointSearch;
        $this->shipmentDetails = $shipmentDetails;
    }

    /**
     * Read ID. Empty if not yet created at Temando platform.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Update ID after order was created at Temando platform.
     *
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Indicates if the order was placed and can be persisted at Temando platform.
     *
     * @return bool
     */
    public function canPersist()
    {
        return (!empty($this->aliases['magento']) && !empty($this->aliases['magentoincrement']));
    }

    /**
     * @return string
     */
    public function getSelectedExperienceCode()
    {
        return $this->selectedExperience->getCode();
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
        $order = [
            'data' => [
                'type' => $this->type,
                'id' => $this->id,
                'attributes' => [
                    'createdAt' => $this->createdAt,
                    'lastModifiedAt' => $this->lastModifiedAt,
                    'orderedAt' => $this->orderedAt,
                    'source' => [
                        'name' => $this->sourceName,
                        'reference' => $this->sourceReference,
                    ],
                    'customer' => $this->customer,
                    'deliverTo' => $this->recipient,
                    'shipmentDetails' => $this->shipmentDetails,
                    'items' => $this->items,
                    'total' => $this->total,
                    'selectedExperience' => $this->selectedExperience
                ],
            ],
            'meta' => [
                'aliases' => $this->aliases,
                'collectionPointSearch' => $this->collectionPointSearch,
            ],
        ];

        foreach ($this->additionalAttributes as $additionalAttribute) {
            $order = ExtensibleTypeProcessor::addAttribute($order, $additionalAttribute);
        }
        $order = AttributeFilter::notEmpty($order);

        return $order;
    }
}
