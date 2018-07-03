/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([
    'jquery',
    'ko',
    'Magento_Ui/js/grid/columns/column'
], function($, ko, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            maxLength: 100
        },

        getLabel: function (row) {
            if (row.connection_name.length <= this.maxLength) {
                return row.connection_name;
            }

            return row.connection_name.substring(0, this.maxLength) + ' …';
        }
    });
});
