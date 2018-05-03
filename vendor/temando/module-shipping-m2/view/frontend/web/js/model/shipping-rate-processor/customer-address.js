/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
define([
    'underscore',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/error-processor',
    'temandoCheckoutFieldsDefinition'
], function (_, resourceUrlManager, quote, storage, shippingService, rateRegistry, errorProcessor, fieldsDefinition) {
    'use strict';

    return {
        /**
         * @param {Object} address
         */
        getRates: function (address) {
            var cache,
                cacheKey;

            if (!address.extensionAttributes) {
                address.extensionAttributes = {};
            }
            if (!address.extensionAttributes.checkoutFields) {
                address.extensionAttributes.checkoutFields = {};
            }

            _.each(fieldsDefinition.getFields(), function (field) {
                address.extensionAttributes.checkoutFields[field.id] = {
                    attributeCode: field.id,
                    value: field.value
                };
            });

            cacheKey = address.getCacheKey().concat(JSON.stringify(address.extensionAttributes));

            shippingService.isLoading(true);
            cache = rateRegistry.get(cacheKey);

            if (cache) {
                shippingService.setShippingRates(cache);
                shippingService.isLoading(false);
            } else {
                storage.post(
                    resourceUrlManager.getUrlForEstimationShippingMethodsByAddressId(quote),
                    JSON.stringify({
                        addressId: address.customerAddressId,
                        extensionAttributes: address.extensionAttributes || {},
                    }),
                    false
                ).done(function (result) {
                    rateRegistry.set(cacheKey, result);
                    shippingService.setShippingRates(result);
                }).fail(function (response) {
                    shippingService.setShippingRates([]);
                    errorProcessor.process(response);
                }).always(function () {
                    shippingService.isLoading(false);
                }
                );
            }
        }
    };
});
