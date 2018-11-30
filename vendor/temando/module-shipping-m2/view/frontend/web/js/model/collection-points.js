/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (_,$,customerData) {
    'use strict';

    var cacheKey = 'collection-point-result';
    var sectionData = customerData.get(cacheKey);


    return {
        getCollectionPoints: function () {
            return sectionData()['collection-points'] || [];
        },

        getMessage: function () {
            var collectionPoints = this.getCollectionPoints();
            var searchRequest = this.getSearchRequest();
            var cpCount = _.size(collectionPoints);

            if (_.isEmpty(searchRequest) || searchRequest.pending === true) {
                return $.mage.__('Enter country and postal code to search for a collection point.');
            } else if (_.isEmpty(collectionPoints) && _.size(searchRequest) > 0) {
                return $.mage.__('No collection points found.');
            } else {
                return $.mage.__('There were %1 results for your search.').replace('%1', cpCount);
            }
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

        selectCollectionPoint: function (entityId) {
            var collectionPoints = this.getCollectionPoints();
            var searchRequest = this.getSearchRequest();

            _.each(collectionPoints, function (collectionPoint) {
                collectionPoint.selected = (collectionPoint.entity_id === entityId);
            });

            customerData.set(cacheKey, {
                'collection-points': collectionPoints,
                'search-request': searchRequest
            });
        },

        getSelectedCollectionPoint: function () {
            var collectionPoints = this.getCollectionPoints();
            var selectedPoint = collectionPoints.filter(function (element) {
                return element.selected;
            });

            if (selectedPoint.length === 0) {
                return false;
            } else {
                return selectedPoint;
            }
        },

        reloadCheckoutData: function () {
            return customerData.reload([cacheKey]);
        },

        clear: function() {
            customerData.set(cacheKey, {
                'collection-points': [],
                'search-request': {}
            });
        }
    };
});
