/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'ko',
    'Temando_Shipping/js/model/collection-points',
    'Temando_Shipping/js/model/pickup-locations'
], function (ko, collectionPoints, pickupLocations) {
    'use strict';


    var userValue = ko.observable('');
    var selected = ko.pureComputed({
        read: function() {
            if (userValue() === '') {
                if (collectionPoints.getSearchRequest()) {
                   return  'toCollectionPoint';
                }
                if (pickupLocations.getSearchRequest()) {
                    return  'clickAndCollect';
                }
                return 'toAddress';
            } else {
                return userValue();
            }
        },
        write: function(value) {
            userValue(value);
        }
    });
    var collectionPointSelected = ko.computed(function () {
        return (selected() === 'toCollectionPoint');
    });
    var toAddressSelected = ko.computed(function () {
        return (selected() === 'toAddress');
    });

    var clickAndCollectSelected = ko.computed(function () {
        return (selected() === 'clickAndCollect');
    });

    return {
        selected: selected,
        isCollectionPointSelected: collectionPointSelected,
        isToAddressSelected: toAddressSelected
    };
});
