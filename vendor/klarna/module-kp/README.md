<h2>Klarna_KP module</h2>

## Overview

The Klarna_Kp module implements the integration with the Klarna Payments payment gateway and makes the latter available as a payment method in Magento.

## Implementation Details

The Klarna_Kp module:

 * adds plugin on `Magento\Payment\Helper\Data::getPaymentMethods` and `getMethodInstance` to add dynamic payment methods from the Klarna Payments API
 * adds plugin on `Magento\Checkout\Block\Checkout\LayoutProcessor::process` to inject checkout specific configuration values
 * adds plugin on `Klarna\Ordermanagement\Controller\Api\Notification::setOrderStatus` to replace the payment method with "klarna_kp" if the payment method is one of the dynamically generated ones from Klarna
 * updates plugin on `Magento\Vault\Plugin\PaymentVaultConfigurationProcess` to adjust the sortOrder such that it runs after the Klarna plugins
 * listens to `payment_method_assign_data` event to associated additional information with an order's payment

## Dependencies

You can find the list of modules that have dependencies on Klarna_Kp module, in the `require` section of the `composer.json` file located in the same directory as this `README.md` file.

## Extension Points

The Klarna_Kp module does not provide any specific extension points. You can extend it using the Magento extension mechanism.

For more information about Magento extension mechanism, see [Magento plug-ins](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/plugins.html) and [Magento dependency injection](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/depend-inj.html).

## Additional information

For more Magento 2 developer documentation, see [Magento 2 Developer Documentation](http://devdocs.magento.com). Also, there you can track [backward incompatible changes made in a Magento EE mainline after the Magento 2.0 release](http://devdocs.magento.com/guides/v2.0/release-notes/changes/ee_changes.html).
