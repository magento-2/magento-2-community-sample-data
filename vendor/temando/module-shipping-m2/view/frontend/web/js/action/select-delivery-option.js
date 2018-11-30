/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Temando_Shipping/js/model/cache-service',
    'Temando_Shipping/js/model/collection-points',
    'Temando_Shipping/js/model/pickup-locations'
], function (_, urlBuilder, customer, storage, quote, shippingService, cacheService, collectionPoints, pickupLocations) {
    'use strict';
    var deliveryOptions = {
        clickAndCollect: pickupLocations,
        toCollectionPoint: collectionPoints
    };
    return function (value) {
        shippingService.isLoading(true);

        var url, urlParams, serviceUrl;
        if (customer.isLoggedIn()) {
            url = '/carts/mine/delivery-option';
            urlParams = {};
        } else {
            url = '/guest-carts/:cartId/delivery-option';
            urlParams = {
                cartId: quote.getQuoteId()
            };
        }
        var payload = {cartId: quote.getQuoteId(), selectedOption: value};
        serviceUrl = urlBuilder.createUrl(url, urlParams);

        return storage.post(
            serviceUrl,
            JSON.stringify(payload)
        ).success(function () {
            cacheService.invalidateCacheForAddress(quote.shippingAddress());
            quote.shippingAddress.valueHasMutated();
            var subscription = shippingService.getShippingRates().subscribe(function () {
                _.each(deliveryOptions, function(option){
                    option.clear();
                });
                if (deliveryOptions[value]) {
                    deliveryOptions[value].reloadCheckoutData();
                }
                subscription.dispose();
            });
        }).fail(function () {
            shippingService.isLoading(false);
        });
    };
});
