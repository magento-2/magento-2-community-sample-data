/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'uiComponent',
    'ko',
    'Temando_Shipping/js/action/save-search-request',
    'Temando_Shipping/js/action/select-search-result',
    'Temando_Shipping/js/model/collection-points'
], function (_, Component, ko, searchAction, selectSearchResult, collectionPoints) {
    'use strict';

    var selectedCollectionPoint = ko.observable(false);
    var readSelected = function () {
        if (selectedCollectionPoint()) {
            return selectedCollectionPoint();
        } else {
            var selected = collectionPoints.getCollectionPoints().find(function (element) {
                return element.selected === "1";
            });

            return selected ? selected.entity_id : false;
        }
    };

    var initializeZipCode =  collectionPoints.getSearchRequestPostCode();
    var initializeCountryCode = collectionPoints.getSearchRequestCountryCode();

    return Component.extend({
        defaults: {
            template: 'Temando_Shipping/checkout/shipping/delivery-options',
            listens: {
                'selectedCollectionPoint': 'onCollectionPointSelect'
            }
        },

        zipCodeError: ko.observable(''),
        zipValue: ko.observable(initializeZipCode),
        countryValue: ko.observable(initializeCountryCode),
        selectedCollectionPoint: selectedCollectionPoint,
        selected: ko.pureComputed({
            read: readSelected,
            write: selectedCollectionPoint,
            owner: this
        }),

        getCountryData: function () {
            var result = [];
            var countryData = window.checkoutConfig['ts-cp-countries'];
            _.each(countryData, function (country) {
                result.push({
                    'countryCode': country.value,
                    'countryName': country.label
                });
            });

            return result;
        },

        onCollectionPointSelect: function (value) {
            selectSearchResult(value);
        },

        getCollectionPoints: function () {
            return collectionPoints.getCollectionPoints();
        },

        getMessage: function () {
            return collectionPoints.getMessage();
        },

        hasNoResult: function () {
            var result = false;
            if (collectionPoints.getSearchRequestPostCode() && this.getCollectionPoints().length < 1) {
                result = true;
            }
            return result;
        },

        /**
         * @return {null}
         */
        submitForm: function () {
            if (this.zipValue().trim().length) {
                // Call request for saving the fields into a table
                searchAction(this.zipValue(), this.countryValue());
                this.zipCodeError('');
            } else {
                this.zipCodeError('This is a required field.');
            }
        }
    });
});
