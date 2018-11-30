/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    'temandoShippingRatesValidator',
    'temandoShippingRatesValidationRules'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    temandoShippingRatesValidator,
    temandoShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('temando', temandoShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('temando', temandoShippingRatesValidationRules);

    return Component;
});
