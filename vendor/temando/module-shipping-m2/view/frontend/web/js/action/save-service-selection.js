/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Temando_Shipping/js/model/cache-service',
    'Magento_Checkout/js/model/shipping-service'
], function (_, urlBuilder, customer, storage, quote, cacheService, shippingService) {
    'use strict';

    return function (serviceSelection) {

        shippingService.isLoading(true);
        var url, urlParams, serviceUrl, payload;
        if (customer.isLoggedIn()) {
            url = '/carts/mine/checkout-fields';
            urlParams = {};
        } else {
            url = '/guest-carts/:cartId/checkout-fields';
            urlParams = {
                cartId: quote.getQuoteId()
            };
        }

        var services = [];
        _.each(serviceSelection, function (service) {
            services.push(
                {
                    attributeCode: service.id,
                    value: service.value()
                }
            );
        });


        payload = {serviceSelection: services};
        serviceUrl = urlBuilder.createUrl(url, urlParams);

        return storage.post(
            serviceUrl,
            JSON.stringify(payload)
        ).success(
            function (response) {
                cacheService.invalidateCacheForAddress(quote.shippingAddress());
                quote.shippingAddress.valueHasMutated();
            }
        ).fail(
            function () {
                shippingService.isLoading(false);
            }
        );
    };
});
