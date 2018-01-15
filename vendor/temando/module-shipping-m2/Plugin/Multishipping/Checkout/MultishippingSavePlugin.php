<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Multishipping\Checkout;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeInterfaceFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Multishipping\Model\Checkout\Type\Multishipping;
use Magento\Quote\Api\Data\AddressExtensionInterfaceFactory;
use Temando\Shipping\Model\Checkout\Schema\CheckoutFieldsSchema;

/**
 * @package  Temando\Shipping\Plugin
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class MultishippingSavePlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AddressExtensionInterfaceFactory
     */
    private $addressExtensionFactory;

    /**
     * @var AttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * @var CheckoutFieldsSchema
     */
    private $schema;

    /**
     * MultishippingSavePlugin constructor.
     * @param RequestInterface $request
     * @param AddressExtensionInterfaceFactory $addressExtensionFactory
     * @param CheckoutFieldsSchema $schema
     * @param AttributeInterfaceFactory $attributeFactory
     */
    public function __construct(
        RequestInterface $request,
        AddressExtensionInterfaceFactory $addressExtensionFactory,
        CheckoutFieldsSchema $schema,
        AttributeInterfaceFactory $attributeFactory
    ) {
        $this->request = $request;
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->schema = $schema;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * Convert user input into attribute types as required for the
     * checkout_fields extension attribute.
     *
     * @param string[] $quoteItem
     * @return AttributeInterface[]
     */
    private function extractCheckoutFields(array $quoteItem)
    {
        $availableFields = $this->schema->getFields();
        $checkoutAttributes = array_intersect_key($quoteItem, $availableFields);

        $checkoutFields = [];

        foreach ($checkoutAttributes as $attributeCode => $value) {
            if (!isset($availableFields[$attributeCode])) {
                continue;
            }

            /** @var \Temando\Shipping\Model\Checkout\Schema\CheckoutField $fieldDefinition */
            $fieldDefinition = $availableFields[$attributeCode];
            if ($fieldDefinition->getType()  === 'checkbox') {
                $value = (bool) $value;
            }

            $attribute = $this->attributeFactory->create();
            $attribute->setAttributeCode($attributeCode);
            $attribute->setValue($value);

            $checkoutFields[$attributeCode] = $attribute;
        }

        return $checkoutFields;
    }

    /**
     * Prevent different checkout field values for the same address.
     *
     * @param AttributeInterface[] $newCheckoutFields
     * @param AttributeInterface[] $originalCheckoutFields
     * @return void
     * @throws LocalizedException
     */
    private function verifyCheckoutFieldSelection($newCheckoutFields, $originalCheckoutFields)
    {
        $msg = __('Please do not use different configurations for the same address.');

        $diff = array_merge(
            array_diff_key($newCheckoutFields, $originalCheckoutFields),
            array_diff_key($originalCheckoutFields, $newCheckoutFields)
        );
        if (!empty($diff)) {
            // fields mismatch detected
            throw new LocalizedException($msg);
        }

        $attributeCodes = array_keys($newCheckoutFields);
        foreach ($attributeCodes as $attributeCode) {
            $newCheckoutField = $newCheckoutFields[$attributeCode];
            $originalCheckoutField = $originalCheckoutFields[$attributeCode];
            if ($newCheckoutField->getValue() !== $originalCheckoutField->getValue()) {
                // field value mismatch detected
                throw new LocalizedException($msg);
            }
        }
    }

    /**
     * Set additional checkout fields selection to shipping address for rates
     * processing in multi shipping checkout.
     *
     * @param Multishipping $subject
     *
     * @return null Argument of original method remains unaltered.
     */
    public function beforeSave(Multishipping $subject)
    {
        $ship = $this->request->getParam('ship');

        if (empty($ship)) {
            return null;
        }

        // keep user input in session, prefill fields in case of error or when going back.
        $subject->getCheckoutSession()->setData('checkoutFieldSelection', $ship);

        foreach ($ship as $quoteItems) {
            foreach ($quoteItems as $itemId => $quoteItem) {
                // obtain shipping address for current quote item
                $addressId = $quoteItem['address'];
                $address = $subject->getQuote()->getShippingAddressByCustomerAddressId($addressId);
                $extensionAttributes = $address->getExtensionAttributes();

                if (!$extensionAttributes) {
                    // address not processed yet; add checkout field selection to extension attributes
                    $extensionAttributes = $this->addressExtensionFactory->create();
                    $checkoutFields = $this->extractCheckoutFields($quoteItem);
                    $extensionAttributes->setCheckoutFields($checkoutFields);
                    $address->setExtensionAttributes($extensionAttributes);
                } else {
                    // address already processed; verify consistency of checkout field selection
                    $checkoutFields = $this->extractCheckoutFields($quoteItem);
                    $this->verifyCheckoutFieldSelection($checkoutFields, $extensionAttributes->getCheckoutFields());
                }
            }
        }

        return null;
    }
}
