/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'uiComponent',
    'ko',
    'temandoCheckoutFieldsDefinition',
    'Magento_Checkout/js/model/quote'
], function (_, Component, ko, fieldsDefinition, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Temando_Shipping/checkout/shipping/checkout-fields',
            fields: []
        },

        /**
         *
         * @returns {*} Array of fields with observable values
         */
        getFields: function () {
            if (_.isEmpty(this.fields)) {
                _.each(fieldsDefinition.getFields(), function (fieldDefinition) {
                    // init field for template rendering
                    var field = {
                        id: fieldDefinition.id,
                        label: fieldDefinition.label,
                        type: fieldDefinition.type,
                        options: fieldDefinition.options,
                        value: ko.observable(fieldDefinition.value)
                    };

                    field.value.subscribe(function (fieldValue) {
                        // on value change, update section data and trigger rate request
                        fieldsDefinition.updateFieldValue(fieldDefinition.id, fieldValue);
                        quote.shippingAddress.valueHasMutated();
                    });

                    // push field definition to component property for template rendering
                    this.fields.push(field);
                }, this);
            }

            return this.fields;
        }
    });
});
