/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

define([], function () {
    'use strict';

    return {
        /**
         * @return {Object}
         */
        getRules: function () {
            return {
                'lastname': {
                    'required': false
                },
                'postcode': {
                    'required': false
                },
                'city': {
                    'required': false
                },
                'country_id': {
                    'required': true
                }
            };
        }
    };
});
