<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface;
use Temando\Shipping\Model\Checkout\Attribute\CheckoutFieldInterface;
use Temando\Shipping\Model\Order\OrderBillingInterface;
use Temando\Shipping\Model\Order\OrderItemInterface;
use Temando\Shipping\Model\Order\OrderRecipientInterface;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeAttribute;
use Temando\Shipping\Rest\Request\Type\ExtensibleTypeAttributeFactory;
use Temando\Shipping\Rest\Request\Type\Generic\DimensionsFactory;
use Temando\Shipping\Rest\Request\Type\Generic\MonetaryValueFactory;
use Temando\Shipping\Rest\Request\Type\Generic\WeightFactory;
use Temando\Shipping\Rest\Request\Type\Order\Customer;
use Temando\Shipping\Rest\Request\Type\Order\CustomerFactory;
use Temando\Shipping\Rest\Request\Type\Order\CollectionPointSearchFactory;
use Temando\Shipping\Rest\Request\Type\Order\ExperienceFactory;
use Temando\Shipping\Rest\Request\Type\Order\Experience\DescriptionFactory;
use Temando\Shipping\Rest\Request\Type\Order\OrderItem;
use Temando\Shipping\Rest\Request\Type\Order\OrderItemFactory;
use Temando\Shipping\Rest\Request\Type\Order\OrderItem\ClassificationCodesFactory;
use Temando\Shipping\Rest\Request\Type\Order\Recipient;
use Temando\Shipping\Rest\Request\Type\Order\RecipientFactory;
use Temando\Shipping\Rest\Request\Type\Order\ShipmentDetails;
use Temando\Shipping\Rest\Request\Type\Order\ShipmentDetailsFactory;
use Temando\Shipping\Rest\Request\Type\OrderRequestTypeInterface;
use Temando\Shipping\Rest\Request\Type\OrderRequestTypeInterfaceFactory;

/**
 * Prepare the request type for order manifestation at the Temando platform.
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderRequestTypeBuilder
{
    /**
     * @var OrderRequestTypeInterfaceFactory
     */
    private $requestTypeFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var RecipientFactory
     */
    private $recipientFactory;

    /**
     * @var ShipmentDetailsFactory
     */
    private $shipmentDetailsFactory;

    /**
     * @var OrderItemFactory
     */
    private $orderItemFactory;

    /**
     * @var ClassificationCodesFactory
     */
    private $classificationCodesFactory;

    /**
     * @var DimensionsFactory
     */
    private $dimensionsFactory;

    /**
     * @var MonetaryValueFactory
     */
    private $monetaryValueFactory;

    /**
     * @var WeightFactory
     */
    private $weightFactory;

    /**
     * @var ExperienceFactory
     */
    private $experienceFactory;

    /**
     * @var DescriptionFactory
     */
    private $descriptionFactory;

    /**
     * @var CollectionPointSearchFactory
     */
    private $collectionPointSearchFactory;

    /**
     * @var ExtensibleTypeAttributeFactory
     */
    private $attributeFactory;

    /**
     * OrderRequestTypeBuilder constructor.
     * @param OrderRequestTypeInterfaceFactory $requestTypeFactory
     * @param CustomerFactory $customerFactory
     * @param RecipientFactory $recipientFactory
     * @param ShipmentDetailsFactory $shipmentDetailsFactory
     * @param OrderItemFactory $orderItemFactory
     * @param ClassificationCodesFactory $classificationCodesFactory
     * @param DimensionsFactory $dimensionsFactory
     * @param MonetaryValueFactory $monetaryValueFactory
     * @param WeightFactory $weightFactory
     * @param ExperienceFactory $experienceFactory
     * @param DescriptionFactory $descriptionFactory
     * @param ExtensibleTypeAttributeFactory $attributeFactory
     * @param CollectionPointSearchFactory $collectionPointSearchFactory
     */
    public function __construct(
        OrderRequestTypeInterfaceFactory $requestTypeFactory,
        CustomerFactory $customerFactory,
        RecipientFactory $recipientFactory,
        ShipmentDetailsFactory $shipmentDetailsFactory,
        OrderItemFactory $orderItemFactory,
        ClassificationCodesFactory $classificationCodesFactory,
        DimensionsFactory $dimensionsFactory,
        MonetaryValueFactory $monetaryValueFactory,
        WeightFactory $weightFactory,
        ExperienceFactory $experienceFactory,
        DescriptionFactory $descriptionFactory,
        ExtensibleTypeAttributeFactory $attributeFactory,
        CollectionPointSearchFactory $collectionPointSearchFactory
    ) {
        $this->requestTypeFactory = $requestTypeFactory;
        $this->customerFactory = $customerFactory;
        $this->recipientFactory = $recipientFactory;
        $this->shipmentDetailsFactory = $shipmentDetailsFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->classificationCodesFactory = $classificationCodesFactory;
        $this->dimensionsFactory = $dimensionsFactory;
        $this->monetaryValueFactory = $monetaryValueFactory;
        $this->weightFactory = $weightFactory;
        $this->experienceFactory = $experienceFactory;
        $this->descriptionFactory = $descriptionFactory;
        $this->attributeFactory = $attributeFactory;
        $this->collectionPointSearchFactory = $collectionPointSearchFactory;
    }

    /**
     * Build customer request type from order billing address entity.
     *
     * @param OrderBillingInterface $billingAddress
     * @return Customer
     */
    private function getCustomerType(OrderBillingInterface $billingAddress)
    {
        $customerType = $this->customerFactory->create([
            'organisationName' => $billingAddress->getCompany(),
            'lastname' => $billingAddress->getLastname(),
            'firstname' => $billingAddress->getFirstname(),
            'email' => $billingAddress->getEmail(),
            'phoneNumber' => $billingAddress->getPhone(),
            'faxNumber' => $billingAddress->getFax(),
            'nationalId' => $billingAddress->getNationalId(),
            'taxId' => $billingAddress->getTaxId(),
            'street' => $billingAddress->getStreet(),
            'countryCode' => $billingAddress->getCountryCode(),
            'administrativeArea' => $billingAddress->getRegion(),
            'postalCode' => $billingAddress->getPostalCode(),
            'locality' => $billingAddress->getCity(),
        ]);

        return $customerType;
    }

    /**
     * Build recipient request type from order shipping address entity.
     *
     * @param OrderRecipientInterface $recipient
     * @param QuoteCollectionPointInterface $collectionPoint
     * @return Recipient
     */
    private function getRecipientType(
        OrderRecipientInterface $recipient,
        QuoteCollectionPointInterface $collectionPoint
    ) {
        if ($collectionPoint->getCollectionPointId()) {
            // collection point recipient
            $recipientType = $this->recipientFactory->create([
                'organisationName' => $collectionPoint->getName(),
                'lastname' => '',
                'firstname' => '',
                'email' => '',
                'phoneNumber' => '',
                'faxNumber' => '',
                'nationalId' => '',
                'taxId' => '',
                'street' => (array) $collectionPoint->getStreet(),
                'countryCode' => $collectionPoint->getCountry(),
                'administrativeArea' => $collectionPoint->getRegion(),
                'postalCode' => $collectionPoint->getPostcode(),
                'locality' => $collectionPoint->getCity(),
                'collectionPointId' => $collectionPoint->getCollectionPointId(),
            ]);
        } else {
            // regular recipient
            $recipientType = $this->recipientFactory->create([
                'organisationName' => $recipient->getCompany(),
                'lastname' => $recipient->getLastname(),
                'firstname' => $recipient->getFirstname(),
                'email' => $recipient->getEmail(),
                'phoneNumber' => $recipient->getPhone(),
                'faxNumber' => $recipient->getFax(),
                'nationalId' => $recipient->getNationalId(),
                'taxId' => $recipient->getTaxId(),
                'street' => (array) $recipient->getStreet(),
                'countryCode' => $recipient->getCountryCode(),
                'administrativeArea' => $recipient->getRegion(),
                'postalCode' => $recipient->getPostalCode(),
                'locality' => $recipient->getCity(),
            ]);
        }

        return $recipientType;
    }

    /**
     * Build shipment details request type from order shipping address entity.
     *
     * @param OrderRecipientInterface $recipient
     * @param QuoteCollectionPointInterface $collectionPoint
     * @return ShipmentDetails
     */
    private function getShipmentDetailsType(
        OrderRecipientInterface $recipient,
        QuoteCollectionPointInterface $collectionPoint
    ) {
        if (!$collectionPoint->getCollectionPointId()) {
            // no final recipient required for regular shipments
            $finalRecipientType = $this->recipientFactory->create([
                'organisationName' => '',
                'lastname' => '',
                'firstname' => '',
                'email' => '',
                'phoneNumber' => '',
                'faxNumber' => '',
                'nationalId' => '',
                'taxId' => '',
                'street' => (array) '',
                'countryCode' => '',
                'administrativeArea' => '',
                'postalCode' => '',
                'locality' => '',
            ]);
        } else {
            $finalRecipientType = $this->recipientFactory->create([
                'organisationName' => $recipient->getCompany(),
                'lastname' => $recipient->getLastname(),
                'firstname' => $recipient->getFirstname(),
                'email' => $recipient->getEmail(),
                'phoneNumber' => $recipient->getPhone(),
                'faxNumber' => $recipient->getFax(),
                'nationalId' => $recipient->getNationalId(),
                'taxId' => $recipient->getTaxId(),
                'street' => (array) $recipient->getStreet(),
                'countryCode' => $recipient->getCountryCode(),
                'administrativeArea' => $recipient->getRegion(),
                'postalCode' => $recipient->getPostalCode(),
                'locality' => $recipient->getCity(),
            ]);
        }

        $shipmentDetailsType = $this->shipmentDetailsFactory->create([
            'finalRecipient' => $finalRecipientType,
            'collectionPointId' => $collectionPoint->getCollectionPointId(),
        ]);

        return $shipmentDetailsType;
    }

    /**
     * Prepare additional request attributes as derived from checkout fields
     * definition and the values added in checkout.
     *
     * @param CheckoutFieldInterface[] $checkoutFields
     * @return ExtensibleTypeAttribute[]
     */
    private function getAdditionalAttributes(array $checkoutFields)
    {
        $additionalAttributes = [];
        foreach ($checkoutFields as $checkoutField) {
            // convert json query path into a stack of hierarchy levels
            $path = explode('/', $checkoutField->getOrderPath());
            array_shift($path);
            $additionalAttribute = $this->attributeFactory->create([
                'attributeId' => $checkoutField->getFieldId(),
                'value'       => $checkoutField->getValue(),
                'dataPath'    => $path,
            ]);

            $additionalAttributes[$additionalAttribute->getAttributeId()] = $additionalAttribute;
        }

        return $additionalAttributes;
    }

    /**
     * @param OrderItemInterface[] $orderItems
     * @return OrderItem[]
     */
    private function getItemTypes(array $orderItems)
    {
        $itemTypes = [];

        foreach ($orderItems as $orderItem) {
            $itemType = $this->orderItemFactory->create([
                'productId' => $orderItem->getProductId(),
                'qty' => $orderItem->getQty(),
                'sku' => $orderItem->getSku(),
                'name' => $orderItem->getName(),
                'description' => $orderItem->getDescription(),
                'categories' => $orderItem->getCategories(),
                'weight' => $this->weightFactory->create([
                    'value' => $orderItem->getWeight(),
                    'unitOfMeasurement' => $orderItem->getWeightUom(),
                ]),
                'unitOfMeasure' => '',
                'dimensions' => $this->dimensionsFactory->create([
                    'length' => $orderItem->getLength(),
                    'width' => $orderItem->getWidth(),
                    'height' => $orderItem->getHeight(),
                    'unit' => $orderItem->getDimensionsUom(),
                ]),
                'monetaryValue' => $this->monetaryValueFactory->create([
                    'amount' => $orderItem->getAmount(),
                    'currency' => $orderItem->getCurrency(),
                ]),
                'isFragile' => $orderItem->isFragile(),
                'isVirtual' => $orderItem->isVirtual(),
                'isPrePackaged' => $orderItem->isPrePackaged(),
                'canRotateVertical' => $orderItem->canRotateVertically(),
                'countryOfOrigin' => $orderItem->getCountryOfOrigin(),
                'countryOfManufacture' => $orderItem->getCountryOfManufacture(),
                'classificationCodes' => $this->classificationCodesFactory->create([
                    'eccn' => $orderItem->getEccn(),
                    'scheduleBinfo' => $orderItem->getScheduleBinfo(),
                    'hsCode' => $orderItem->getHsCode(),
                ]),
            ]);
            $itemTypes[] = $itemType;
        }

        return $itemTypes;
    }

    /**
     * Create order request type from order entity. Submitting an order ID to
     * the Temando platform manifests the order.
     *
     * @param OrderInterface $order
     * @return OrderRequestTypeInterface
     * @throws \Exception
     */
    public function build(OrderInterface $order)
    {
        $requestTypeData = [
            'id' => $order->getOrderId(),
            'createdAt' => date_create($order->getCreatedAt())->format('c'),
            'lastModifiedAt' => date_create($order->getLastModifiedAt())->format('c'),
            'orderedAt' => date_create($order->getOrderedAt())->format('c'),
            'sourceName' => 'Magento',
            'sourceReference' => $order->getSourceReference(),
            'total' => $this->monetaryValueFactory->create([
                'amount' => $order->getAmount(),
                'currency' => $order->getCurrency(),
            ]),
            'customer' => $this->getCustomerType($order->getBilling()),
            'recipient' => $this->getRecipientType($order->getRecipient(), $order->getCollectionPoint()),
            'shipmentDetails' => $this->getShipmentDetailsType($order->getRecipient(), $order->getCollectionPoint()),
            'items' => $this->getItemTypes($order->getOrderItems()),
            'aliases' => [
                'magento' => $order->getSourceId(),
                'magentoincrement' => $order->getSourceIncrementId(),
            ],
            'selectedExperience' => $this->experienceFactory->create([
                'code' => $order->getExperienceCode(),
                'cost' => $this->monetaryValueFactory->create([
                    'amount' => $order->getExperienceAmount(),
                    'currency' => $order->getExperienceCurrency(),
                ]),
                'description' => $this->descriptionFactory->create([
                    'language' => $order->getExperienceLanguage(),
                    'text' => $order->getExperienceDescription(),
                ]),
            ]),
            'collectionPointSearch' => $this->collectionPointSearchFactory->create([
                'postalCode' => $order->getCollectionPointSearchRequest()->getPostcode(),
                'countryCode' => $order->getCollectionPointSearchRequest()->getCountryId(),
            ]),
        ];

        if ($order->getCollectionPoint()->getCollectionPointId()) {
            // no search if collection point was already selected
            unset($requestTypeData['collectionPointSearch']);
        }

        $orderType = $this->requestTypeFactory->create($requestTypeData);

        // internal types (as is)
        $checkoutFields = $order->getCheckoutFields();
        // request types (prepared for API usage)
        $additionalAttributes = $this->getAdditionalAttributes($checkoutFields);
        foreach ($additionalAttributes as $additionalAttribute) {
            $orderType->addAdditionalAttribute($additionalAttribute);
        }

        return $orderType;
    }
}
