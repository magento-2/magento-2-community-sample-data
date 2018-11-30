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
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterfaceFactory;
use Temando\Shipping\Model\Checkout\Schema\CheckoutFieldsSchema;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;

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
     * @var AttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var CheckoutFieldsSchema
     */
    private $schema;

    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * MultishippingSavePlugin constructor.
     *
     * @param RequestInterface           $request
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory    $addressFactory
     * @param CheckoutFieldsSchema       $schema
     * @param AttributeInterfaceFactory  $attributeFactory
     * @param ModuleConfigInterface      $moduleConfig
     * @param StoreManagerInterface      $storeManager
     */
    public function __construct(
        RequestInterface $request,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressFactory,
        CheckoutFieldsSchema $schema,
        AttributeInterfaceFactory $attributeFactory,
        ModuleConfigInterface $moduleConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->request           = $request;
        $this->addressRepository = $addressRepository;
        $this->addressFactory    = $addressFactory;
        $this->schema            = $schema;
        $this->attributeFactory  = $attributeFactory;
        $this->moduleConfig      = $moduleConfig;
        $this->storeManager      = $storeManager;
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
    public function afterSave(Multishipping $subject, $result)
    {
        if (!$this->moduleConfig->isEnabled($this->storeManager->getStore()->getId())) {
            return $result;
        }

        $ship = $this->request->getParam('ship');

        if (empty($ship)) {
            return null;
        }

        // keep user input in session, prefill fields in case of error or when going back.
        $subject->getCheckoutSession()->setData('checkoutFieldSelection', $ship);

        foreach ($ship as $quoteItems) {
            foreach ($quoteItems as $itemId => $quoteItem) {
                // Skip item if it has no address (virtual or downloadable...)
                if (!isset($quoteItem['address'])) {
                    continue;
                }
                // obtain shipping address for current quote item
                $addressId = $quoteItem['address'];
                $shippingAddress = $subject->getQuote()->getShippingAddressByCustomerAddressId($addressId);

                try {
                    $address = $this->addressRepository->getByQuoteAddressId($shippingAddress->getId());
                } catch (LocalizedException $e) {
                    $address = $this->addressFactory->create(['data' => [
                        AddressInterface::SHIPPING_ADDRESS_ID => $shippingAddress->getId()
                    ]]);
                }
                $checkoutFields = $this->extractCheckoutFields($quoteItem);
                if ($address->getServiceSelection()) {
                    $this->verifyCheckoutFieldSelection($checkoutFields, $address->getServiceSelection());
                }

                $address->setServiceSelection($checkoutFields);
                $this->addressRepository->save($address);

                // re-collect rates with checkout field selection
                $shippingAddress->setCollectShippingRates(true);
                $shippingAddress->collectShippingRates();
            }
        }

        return $result;
    }
}
