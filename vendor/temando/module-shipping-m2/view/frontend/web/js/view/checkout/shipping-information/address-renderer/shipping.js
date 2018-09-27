/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Temando_Shipping/js/model/collection-points'
], function (_, Component, customerData, collectionPoints) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-information/address-renderer/default'
        },
        collectionPoints: collectionPoints,

        /**
         * @param {*} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        getRegionNameByCode: function (countryId, regionCode) {
            var result = regionCode;
            var countryRegions = countryData()[countryId].regions || {};

            if (_.size(countryRegions) > 0) {
                var region = _.filter(countryRegions, (function (element) {
                        return element.code === regionCode;
                    })
                );

                if (region.length > 0) {
                    result = region[0].name;
                }
            }

            return result;
        },

        getTemplate: function () {
            var collectionPoint = collectionPoints.getSelectedCollectionPoint();
            if (collectionPoint) {
                return 'Temando_Shipping/checkout/shipping/address-renderer/collection-point';
            }
            // handle other specific adresses here
            return this.template;
        }
    });
});
