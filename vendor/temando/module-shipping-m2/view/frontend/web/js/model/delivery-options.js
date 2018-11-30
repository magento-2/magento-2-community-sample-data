/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'ko',
    'Temando_Shipping/js/model/collection-points'
], function (ko, collectionPoints) {
    'use strict';


    var userValue = ko.observable('');
    var selected = ko.pureComputed({
        read: function() {
            if (userValue() === '') {
                return collectionPoints.getSearchRequest() ? 'toCollectionPoint' : 'toAddress';
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

    return {
        selected: selected,
        isCollectionPointSelected: collectionPointSelected,
        isToAddressSelected: toAddressSelected
    };
});
