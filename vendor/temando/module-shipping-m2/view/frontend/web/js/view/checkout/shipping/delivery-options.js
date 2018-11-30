/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'ko',
    'uiComponent',
    'temandoDeliveryOptions',
    'Temando_Shipping/js/action/select-delivery-option'
], function (ko, Component, deliveryOptions, selectDeliveryOption) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Temando_Shipping/checkout/shipping/delivery-options'
        },

        deliveryOptions: ko.observableArray(window.checkoutConfig['delivery-options']),
        radioSelectedOptionValue: deliveryOptions.selected,
        collectionPointsVisible: ko.computed(function () {
            return deliveryOptions.selected() === 'toCollectionPoint';
        }),
        clickAndCollectVisible: ko.computed(function () {
            return deliveryOptions.selected() === 'clickAndCollect';
        }),

        initialize: function () {
            this._super();
            var self = this;
            self.radioSelectedOptionValue.subscribe(function (value) {
                selectDeliveryOption(value);
            });
        }
    });
});
