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
    'Klarna_Kp/js/model/config'
  ],
  function (config) {
    'use strict';
    return {
      log: function (message) {
        if (config.debug) {
          console.trace();
          console.log(message);
        }
      },
      group: function (groupid) {
        if (config.debug) {
          console.group(groupid);
        }
      },
      groupEnd: function () {
        if (config.debug) {
          console.groupEnd();
        }
      },
      table: function (tabularData, properties) {
        if (config.debug) {
          console.trace();
          console.table(tabularData, properties);
        }
      }
    };
  }
);
