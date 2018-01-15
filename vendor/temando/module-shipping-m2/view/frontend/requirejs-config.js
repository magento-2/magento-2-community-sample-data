var config = {
    paths: {
        temandoCheckoutFieldsDefinition: 'Temando_Shipping/js/model/fields-definition',
        temandoCustomerAddressRateProcessor: 'Temando_Shipping/js/model/shipping-rate-processor/customer-address',
        temandoNewAddressRateProcessor: 'Temando_Shipping/js/model/shipping-rate-processor/new-address',
        temandoShippingRatesValidator: 'Temando_Shipping/js/model/shipping-rates-validator/temando',
        temandoShippingRatesValidationRules: 'Temando_Shipping/js/model/shipping-rates-validation-rules/temando'
    },
    map: {
        'Magento_Checkout/js/model/shipping-rate-service': {
            'Magento_Checkout/js/model/shipping-rate-processor/customer-address' : 'temandoCustomerAddressRateProcessor',
            'Magento_Checkout/js/model/shipping-rate-processor/new-address' : 'temandoNewAddressRateProcessor'
        }
    }
};
