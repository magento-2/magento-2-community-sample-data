/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Temando_Shipping/js/model/cache-service',
    'Magento_Checkout/js/model/shipping-service'
], function (urlBuilder, customer, storage, quote, cacheService, shippingService) {
    'use strict';

    return function (selectedValue) {

        var url, urlParams, serviceUrl;
        if (customer.isLoggedIn()) {
            url = '/carts/mine/collection-point/select';
            urlParams = {};
        } else {
            url = '/guest-carts/:cartId/collection-point/select';
            urlParams = {
                cartId: quote.getQuoteId()
            };
        }
        var payload = {entityId: selectedValue};
        serviceUrl = urlBuilder.createUrl(url, urlParams);

        shippingService.isLoading(true);

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
