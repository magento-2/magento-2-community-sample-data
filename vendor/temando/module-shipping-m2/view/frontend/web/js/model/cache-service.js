/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'Magento_Checkout/js/model/shipping-rate-registry'
], function (rateRegistry) {
    'use strict';

    return {
        invalidateCacheForAddress: function(address) {
            var cacheKey = address.getCacheKey();
            rateRegistry.set(cacheKey, null);
        }
    };
});
