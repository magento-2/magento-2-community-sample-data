/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'Magento_Customer/js/customer-data'
], function (_, customerData) {
    'use strict';

    var cacheKey = 'collection-point-result';
    var sectionData = customerData.get(cacheKey);


    return {
        getCollectionPoints: function () {
            return sectionData()['collection-points'] || [];
        },

        getMessage: function () {
            return sectionData().message || '';
        },

        getSearchRequest: function () {
            if (_.size(sectionData()['search-request']) > 0) {
                return sectionData()['search-request'];
            }
            return false;
        },

        getSearchRequestCountryCode: function () {
            return this.getSearchRequest().country_id || '';
        },

        getSearchRequestPostCode: function () {
            return this.getSearchRequest().postcode || '';
        },

        getSelectedCollectionPoint: function () {
            var points = this.getCollectionPoints();
            var selectedPoint = points.filter(function (element) {
                return element.selected === "1";
            });
            if(selectedPoint.length === 0){
                return false;
            } else {
                return selectedPoint;
            }
        },

        reloadCheckoutData: function () {
            return customerData.reload([cacheKey]);
        }
    };
});
