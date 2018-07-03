/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'underscore',
    'ko',
    'Magento_Customer/js/customer-data'
], function (_, ko, customerData) {
    'use strict';

    var cacheKey = 'checkout-fields';

    return {
        getFields: function () {
            var sectionData = customerData.get(cacheKey);

            _.each(sectionData().fields, function (field) {
                if ((field.value === undefined) && field.defaultValue) {
                    field.value = field.defaultValue;
                }
            });

            return sectionData().fields;
        },

        updateFieldValue: function (fieldId, fieldValue) {
            var sectionData = customerData.get(cacheKey);

            if (fieldValue === undefined) {
                fieldValue = '';
            }

            _.each(sectionData().fields, function (field) {
                if (field.id === fieldId) {
                    field.value = fieldValue;
                }
            });

            customerData.set(cacheKey, sectionData());
        }
    };
});
