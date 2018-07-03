/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Temando_Shipping/js/model/cache-service',
    'Magento_Checkout/js/model/shipping-service',
    'Temando_Shipping/js/model/collection-points'
], function (urlBuilder, customer, storage, quote, cacheService, shippingService, collectionPoints) {
    'use strict';

    return function (postCode, countryId) {

        shippingService.isLoading(true);
        var url, urlParams, serviceUrl, payload;
        if (customer.isLoggedIn()) {
            url = '/carts/mine/collection-point/search-request';
            urlParams = {};
        } else {
            url = '/guest-carts/:cartId/collection-point/search-request';
            urlParams = {
                cartId: quote.getQuoteId()
            };
        }
        payload = {postcode: postCode, countryId: countryId};
        serviceUrl = urlBuilder.createUrl(url, urlParams);

        return storage.put(
            serviceUrl,
            JSON.stringify(payload)
        ).success(
            function (response) {
                cacheService.invalidateCacheForAddress(quote.shippingAddress());

                quote.shippingAddress.valueHasMutated();

                var subscription = shippingService.getShippingRates().subscribe(function() {
                    shippingService.isLoading(true);
                    collectionPoints.reloadCheckoutData().always(function () {
                        shippingService.isLoading(false);
                    });
                    subscription.dispose();
                });


            }
        ).fail(
            function () {
                shippingService.isLoading(false);
            }
        );
    };
});
