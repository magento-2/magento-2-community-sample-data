<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type;

/**
 * Temando API Fulfillment
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class FulfillmentRequestType implements FulfillmentRequestTypeInterface, \JsonSerializable
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
    private $reference;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $pickupLocationId;

    /**
     * @var string[]
     */
    private $items;

    /**
     * FulfillmentRequestType constructor.
     * @param string $id
     * @param string $type
     * @param string $reference
     * @param string $state
     * @param string $orderId
     * @param string $pickupLocationId
     * @param string[] $items
     */
    public function __construct(
        $id,
        $type,
        $reference,
        $state,
        $orderId,
        $pickupLocationId,
        array $items
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->reference = $reference;
        $this->state = $state;
        $this->orderId = $orderId;
        $this->pickupLocationId = $pickupLocationId;
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string[] $operations
     * @return string[]
     */
    private function addStateUpdate(array $operations)
    {
        if (empty($this->state)) {
            return $operations;
        }

        $operations[]= [
            'op' => 'replace',
            'path' => '/state',
            'value' => $this->state,
        ];

        return $operations;
    }

    /**
     * @param string[] $operations
     * @return string[]
     */
    private function addItemsUpdate(array $operations)
    {
        if (empty($this->items)) {
            return $operations;
        }

        $items = array_map(function ($sku) {
            return [
                'quantity' => $this->items[$sku],
                'product' => ['sku' => $sku]
            ];
        }, array_keys($this->items));

        $operations[]= [
            'op' => 'replace',
            'path' => '/items',
            'value' => $items,
        ];

        return $operations;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize()
    {
        if ($this->id) {
            // update
            $fulfillment = [];
            $fulfillment = $this->addStateUpdate($fulfillment);
            $fulfillment = $this->addItemsUpdate($fulfillment);
        } else {
            // create
            $items = array_map(function ($sku) {
                return [
                    'quantity' => $this->items[$sku],
                    'product' => ['sku' => $sku],
                ];
            }, array_keys($this->items));

            $fulfillment = [
                'data' => [
                    'type' => $this->type,
                    'attributes' => [
                        'reference' => $this->reference,
                        'items' => $items,
                    ],
                    'relationships' => [
                        'order' => [
                            'data' => [
                                'type' => 'order',
                                'id' => $this->orderId,
                            ]
                        ],
                        'pickUpLocation' => [
                            'data' => [
                                'type' => 'location',
                                'id' => $this->pickupLocationId,
                            ]
                        ]
                    ],
                ],
            ];
        }

        return $fulfillment;
    }
}
