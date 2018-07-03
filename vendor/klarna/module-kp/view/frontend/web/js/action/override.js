/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
  'mage/utils/wrapper',
  'Klarna_Kp/js/model/config',
  'Magento_Checkout/js/model/full-screen-loader'
], function (wrapper, config, loader) {
  'use strict';

  /**
   * This is needed to prevent the customer from a race condition between 'Place Order' and adding/removing a coupon,
   * giftcard, rewards points, etc.. as it affects order totals
   */
  return function (overriddenFunction) {
    return wrapper.wrap(overriddenFunction, function (originalAction) {
      if (!config.enabled) {
        return originalAction();
      }
      if (config.hasErrors()) {
        return originalAction();
      }
      loader.startLoader();
      return originalAction().then(function () {
        loader.stopLoader();
      });
    });
  };
});
