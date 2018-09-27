/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'uiComponent',
    'temandoShippingComponentry',
], function ($, Component, temando) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();

            //start of initialisation process
            var componentData = $('#' + this.ns).data('component-init')[0];
            var elements = {main: $('#' + this.ns)[0]};
            if (componentData.elements && componentData.elements.m2PageActionsButtonsId) {
                elements.m2PageActionsButtons = $('#' + componentData.elements.m2PageActionsButtonsId)[0];
            }

            temando.init({
                entrypoint: componentData.entrypoint,
                assetsUrl: componentData.assetsUrl,
                elements: elements,
                data: componentData.data
            });
        },
    });
});
