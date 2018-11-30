/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (_, $, customerData) {
    'use strict';

    var cacheKey = 'pickup-location-result';
    var sectionData = customerData.get(cacheKey);


    return {
        getPickupLocations: function () {
            return sectionData()['pickup-locations'] || [];
        },

        getMessage: function () {
            var locations = this.getPickupLocations();
            var searchRequest = this.getSearchRequest();
            var locationsCount = _.size(locations);

            if (_.isEmpty(searchRequest)) {
                return $.mage.__('Please wait.');
            } else if (_.isEmpty(locations) && _.size(searchRequest) > 0) {
                return $.mage.__('No pickup locations found.');
            } else {
                return $.mage.__('There were %1 results for your search.').replace('%1', locationsCount);
            }
        },

        getSearchRequest: function () {
            if (_.size(sectionData()['search-request']) > 0) {
                return sectionData()['search-request'];
            }
            return false;
        },

        selectPickupLocation: function (entityId) {
            var pickupLocations = this.getPickupLocations();
            var searchRequest = this.getSearchRequest();

            _.each(pickupLocations, function (pickupLocation) {
                pickupLocation.selected = (pickupLocation.entity_id === entityId);
            });

            customerData.set(cacheKey, {
                'pickup-locations': pickupLocations,
                'search-request': searchRequest
            });
        },

        getSelectedPickupLocation: function () {
            var locations = this.getPickupLocations();
            var selectedLocation = locations.filter(function (element) {
                return element.selected;
            });

            if (selectedLocation.length === 0) {
                return false;
            } else {
                return selectedLocation;
            }
        },

        reloadCheckoutData: function () {
            return customerData.reload([cacheKey]);
        },

        clear: function() {
            customerData.set(cacheKey, {
                'pickup-locations': [],
                'search-request': {}
            });
        }
    };
});
