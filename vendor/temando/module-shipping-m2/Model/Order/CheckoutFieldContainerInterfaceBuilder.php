<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Framework\Api\AbstractSimpleObjectBuilder;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressExtensionInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Api\Data\OrderAddressExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\Checkout\Attribute\CheckoutFieldInterface;
use Temando\Shipping\Model\Checkout\Attribute\CheckoutFieldInterfaceFactory;
use Temando\Shipping\Model\Checkout\RateRequest\Extractor;
use Temando\Shipping\Model\Checkout\Schema\CheckoutFieldsSchema;

/**
 * Temando Order Checkout Field Container Builder
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CheckoutFieldContainerInterfaceBuilder extends AbstractSimpleObjectBuilder
{
    /**
     * @var Extractor
     */
    private $rateRequestExtractor;

    /**
     * @var CheckoutFieldsSchema
     */
    private $schema;

    /**
     * @var CheckoutFieldInterfaceFactory
     */
    private $fieldFactory;

    /**
     * @param ObjectFactory $objectFactory
     * @param Extractor $rateRequestExtractor
     * @param CheckoutFieldsSchema $schema
     * @param CheckoutFieldInterfaceFactory $fieldFactory
     */
    public function __construct(
        ObjectFactory $objectFactory,
        Extractor $rateRequestExtractor,
        CheckoutFieldsSchema $schema,
        CheckoutFieldInterfaceFactory $fieldFactory
    ) {
        $this->rateRequestExtractor = $rateRequestExtractor;
        $this->schema = $schema;
        $this->fieldFactory = $fieldFactory;

        parent::__construct($objectFactory);
    }

    /**
     * @param \Magento\Framework\Api\AttributeInterface[] $checkoutAttributes
     * @return CheckoutFieldInterface[]
     */
    private function getCheckoutFieldsFromAttributes(array $checkoutAttributes)
    {
        $availableFields = $this->schema->getFields();

        // remove all checkout attributes that are not/no longer in configured fields.
        $fnRemoveUnavailableFields = function (AttributeInterface $checkoutAttribute) use ($availableFields) {
            return in_array($checkoutAttribute->getAttributeCode(), array_keys($availableFields));
        };
        $checkoutAttributes = array_filter($checkoutAttributes, $fnRemoveUnavailableFields);

        // convert checkout attributes to checkout fields (add data path)
        $checkoutFields = array_map(function (AttributeInterface $checkoutAttribute) use ($availableFields) {
            /** @var \Temando\Shipping\Model\Checkout\Schema\CheckoutField $fieldDefinition */
            $fieldDefinition = $availableFields[$checkoutAttribute->getAttributeCode()];

            $checkoutField = $this->fieldFactory->create(['data' => [
                CheckoutFieldInterface::FIELD_ID => $checkoutAttribute->getAttributeCode(),
                CheckoutFieldInterface::VALUE => $checkoutAttribute->getValue(),
                CheckoutFieldInterface::ORDER_PATH => $fieldDefinition->getOrderPath(),
            ]]);

            return $checkoutField;
        }, $checkoutAttributes);

        return $checkoutFields;
    }

    /**
     * Set value as selected during checkout (rate request)
     *
     * For some reason the shipping method management turns the well defined
     * extension attribute into an untyped array. Dealing with it here.
     *
     * @see \Magento\Quote\Model\ShippingMethodManagement::getShippingMethods
     * @see \Magento\Quote\Model\ShippingMethodManagement::extractAddressData
     *
     * @param RateRequest $rateRequest
     * @return void
     */
    public function setRateRequest(RateRequest $rateRequest)
    {
        try {
            $shippingAddress = $this->rateRequestExtractor->getShippingAddress($rateRequest);
            $extensionAttributes = $shippingAddress->getExtensionAttributes();
            if ($extensionAttributes instanceof AddressExtensionInterface
                && is_array($extensionAttributes->getCheckoutFields())
            ) {
                $checkoutFields = $this->getCheckoutFieldsFromAttributes($extensionAttributes->getCheckoutFields());
            } else {
                $checkoutFields = [];
            }
        } catch (LocalizedException $e) {
            // detailed address data unavailable
            $checkoutFields = [];
        }

        $this->_set(CheckoutFieldContainerInterface::FIELDS, $checkoutFields);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     * @return void
     */
    public function setOrder(OrderInterface $order)
    {
        $shippingAddress = $order->getShippingAddress();

        $extensionAttributes = $shippingAddress->getExtensionAttributes();
        if ($extensionAttributes instanceof OrderAddressExtensionInterface
            && is_array($extensionAttributes->getCheckoutFields())
        ) {
            $checkoutFields = $this->getCheckoutFieldsFromAttributes($extensionAttributes->getCheckoutFields());
        } else {
            $checkoutFields = [];
        }

        $this->_set(CheckoutFieldContainerInterface::FIELDS, $checkoutFields);
    }
}
