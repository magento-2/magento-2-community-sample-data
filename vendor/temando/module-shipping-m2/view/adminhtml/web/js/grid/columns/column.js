/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/grid/columns/column'
], function ($, ko, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            maxLength: 60,
            linkTitleLess: "Show less",
            linkTitleMore: "Show more"
        },

        initRowData: function (row) {
            if (row.active_services === undefined) {
                row.active_services = ko.observable("");
            }

            if (row.active_services.length >= this.maxLength) {
                row.services_expandable = ko.observable(true);
                row.services_open = ko.observable(true);
                row.services_link = ko.observable('');
                row.active_services = ko.observable(row.active_services);
                row.active_services_full = ko.observable(row.active_services());
                this.truncate(row);
            } else {
                row.services_expandable = ko.observable(false);
                row.active_services = ko.observable(row.active_services);
            }
        },

        toggleShowFull: function (data, row) {
            this.truncate(row);
        },

        getLabel: function (row) {
            if (!row.hasOwnProperty("services_expandable")) {
                this.initRowData(row);
            }
            return row.active_services();
        },

        truncate: function (row) {
            var displayText = row.active_services_full();

            if (row.services_open() === true) {
                row.active_services(displayText.substring(0, this.maxLength) + ' …');
                row.services_open(false);
                row.services_link(this.linkTitleMore);
            } else {
                row.active_services(displayText);
                row.services_open(true);
                row.services_link(this.linkTitleLess);
            }
        }
    });
});
