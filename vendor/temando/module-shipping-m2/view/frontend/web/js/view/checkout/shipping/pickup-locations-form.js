/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'uiComponent',
    'ko',
    'Temando_Shipping/js/model/pickup-locations',
    'Temando_Shipping/js/action/select-pickup-location'
], function (_, Component, ko, pickupLocations, selectPickupLocationAction) {
    'use strict';

    var selectedPickupLocation = ko.observable(false);
    var readSelected = function () {
        if (selectedPickupLocation()) {
            return selectedPickupLocation();
        } else {
            var selected = pickupLocations.getPickupLocations().find(function (element) {
                return element.selected;
            });

            return selected ? selected.entity_id : false;
        }
    };

    return Component.extend({
        defaults: {
            template: 'Temando_Shipping/checkout/shipping/delivery-options',
            listens: {
                'selectedPickupLocation': 'onPickupLocationSelect'
            }
        },

        selectedPickupLocation: selectedPickupLocation,
        selected: ko.pureComputed({
            read: readSelected,
            write: selectedPickupLocation,
            owner: this
        }),

        onPickupLocationSelect: function (value) {
            selectPickupLocationAction(value);
            pickupLocations.selectPickupLocation(value);
        },

        getPickupLocations: function () {
            return pickupLocations.getPickupLocations();
        },

        getMessage: function () {
            return pickupLocations.getMessage();
        },

        hasNoResult: function () {
            var result = false;
            if (this.getPickupLocations().length < 1) {
                result = true;
            }
            return result;
        }
    });
});
