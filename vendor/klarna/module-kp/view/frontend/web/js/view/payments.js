/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
  [
    'underscore',
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list',
    'Klarna_Kp/js/model/config'
  ],
  function (_,
            Component,
            rendererList,
            config) {
    'use strict';
    if (config.available_methods && _.isArray(config.available_methods)) {
      config.available_methods.forEach(function (value) {
        rendererList.push(value);
      });
    }
    // Add view logic here if needed
    return Component.extend({});
  }
);
