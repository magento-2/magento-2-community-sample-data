/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
define([
    'uiComponent',
    'ko'
], function (Component, ko) {
    "use strict";

    return Component.extend({
        initialize: function () {
            var self = this;

            this._super();
            this.sync.checked = ko.observable(this.sync.checked);
            this.elements.forEach(function (element) {
                element.checked = ko.observable(element.checked);
                element.disabled = ko.observable(element.disabled);
            });

            this.sync.checked.subscribe(function (value) {
                if (value === false) {
                    self.elements.forEach(function (element) {
                        element.disabled(true);
                    });
                }
            });

        }
    });
});
