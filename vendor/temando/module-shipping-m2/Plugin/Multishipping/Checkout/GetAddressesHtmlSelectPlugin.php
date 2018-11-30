<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Multishipping\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Multishipping\Block\Checkout\Addresses;
use Magento\Quote\Model\Quote\Address\Item;
use Temando\Shipping\Model\Checkout\Schema\CheckoutField;
use Temando\Shipping\Model\Checkout\Schema\CheckoutFieldsSchema;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * @package Temando\Shipping\Plugin
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetAddressesHtmlSelectPlugin
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * @var CheckoutFieldsSchema
     */
    private $schema;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * GetAddressesHtmlSelectPlugin constructor.
     *
     * @param ModuleConfigInterface $moduleConfig
     * @param CheckoutFieldsSchema  $schema
     * @param Session $checkoutSession
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        CheckoutFieldsSchema $schema,
        Session $checkoutSession
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->schema = $schema;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param CheckoutField $field
     * @param string $elementId
     * @param string $elementName
     * @param string $value
     * @return string
     */
    private function renderCheckboxField(CheckoutField $field, $elementId, $elementName, $value)
    {
        $value = $value ? : $field->getDefaultValue();

        $fieldHtml = sprintf(
            '<input type="%s" id="%s" name="%s" %s>',
            $field->getType(),
            $elementId,
            $elementName,
            $value ? 'checked="checked"' : ''
        );

        return $fieldHtml;
    }

    /**
     * @param CheckoutField $field
     * @param string $elementId
     * @param string $elementName
     * @param string $value
     * @return string
     */
    private function renderInputField(CheckoutField $field, $elementId, $elementName, $value)
    {
        $value = $value ? : $field->getDefaultValue();

        $fieldHtml = sprintf(
            '<input type="%s" id="%s" name="%s" value="%s">',
            $field->getType(),
            $elementId,
            $elementName,
            $value
        );

        return $fieldHtml;
    }

    /**
     * @param CheckoutField $field
     * @param string $elementId
     * @param string $elementName
     * @param string $value
     * @return string
     */
    private function renderSelectField(CheckoutField $field, $elementId, $elementName, $value)
    {
        $value = $value ? : $field->getDefaultValue();
        $options = '';
        $fieldOptions = $field->getOptions();

        foreach ($fieldOptions as $option) {
            $options.= sprintf(
                '<option value="%s" %s>%s</option>',
                $option['value'],
                ($value === (string) $option['value']) ? 'selected="selected"' : '',
                $option['name']
            );
        }

        $fieldHtml = sprintf(
            '<select id="%s" name="%s">%s</select>',
            $elementId,
            $elementName,
            $options
        );

        return $fieldHtml;
    }

    /**
     * Render the markup for one field.
     * Set value from session if available, from configured default value otherwise.
     *
     * @param CheckoutField $field
     * @param int $index
     * @param string $itemId
     * @return string
     */
    private function renderField(CheckoutField $field, $index, $itemId)
    {
        $value = '';
        $checkoutFieldSelection = $this->checkoutSession->getData('checkoutFieldSelection');

        if (isset($checkoutFieldSelection[$index])
            && isset($checkoutFieldSelection[$index][$itemId])
        ) {
            $fields = $checkoutFieldSelection[$index][$itemId];
            $value  = isset($fields[$field->getId()]) ?  $fields[$field->getId()] :  null;
        }

        $elementName = 'ship[' . $index . '][' . $itemId . ']['. $field->getId() . ']';
        $elementId   = 'ship_' . $index . '_' . $itemId . '_' . $field->getId();

        $labelHtml = '<label for="' . $elementId . '"><span>'. $field->getLabel() .'</span></label>';
        $html = '';

        switch ($field->getType()) {
            case 'checkbox':
                $fieldHtml = $this->renderCheckboxField($field, $elementId, $elementName, $value);
                $html = $fieldHtml . $labelHtml;
                break;
            case 'text':
            case 'number':
                $fieldHtml = $this->renderInputField($field, $elementId, $elementName, $value);
                $html = $labelHtml . $fieldHtml;
                break;
            case 'select':
                $fieldHtml = $this->renderSelectField($field, $elementId, $elementName, $value);
                $html = $labelHtml . $fieldHtml;
                break;
        }

        return '<div class="field '. $field->getType() .'">' . $html . '</div>';
    }

    /**
     * @param Addresses $subject
     * @param string $result
     * @param Item $item
     * @param int $index
     * @return string
     */
    public function afterGetAddressesHtmlSelect(Addresses $subject, $result, Item $item, $index)
    {
        $storeId = $subject->getCheckout()->getQuote()->getStoreId();
        if (!$this->moduleConfig->isEnabled($storeId)) {
            return $result;
        }

        $checkoutFields = $this->schema->getFields();
        if (empty($checkoutFields)) {
            return $result;
        }

        $fieldsHtml = '';
        foreach ($checkoutFields as $checkoutField) {
            $fieldsHtml.= $this->renderField($checkoutField, $index, $item->getQuoteItemId());
        }

        $fieldsHtml = '<div class="multi-checkout-fields">' . $fieldsHtml . '</div>';

        return $result . $fieldsHtml;
    }
}
