# Vertex Tax Links for Magento 2

The Vertex Tax Links for Magento 2 module allows merchants to utilize Vertex Tax Links or Vertex Cloud to provide the relevant tax calculations for US-based sales.

## Public API

Vertex Tax Links provides a few key places for integration and customization.  These are all marked with the `@api` annotation.

**Note**: Anything not marked with the `@api` annotation may be modified or removed in future versions, unless specified in below list of public API access points

* `\Vertex\Tax\Api\LogEntryRepositoryInterface`
  Use this class to interface with the default Log Entry datastore (the database), or provide a preference to store logs in an external system.
* `\Vertex\Tax\Api\Data\LogEntryInterface`
  This interface represents a Log Entry.
* `\Vertex\Tax\Api\Data\LogEntrySearchResultsInterface`
  This interface represents a list of results for a search against the `LogEntryRepositoryInterface`
* `\Vertex\Tax\Api\ClientInterface::sendApiRequest`
  Intercept this method to modify the SOAP Request/Response to/from Vertex
* `vertex_customer_code` extension attribute on `\Magento\Customer\Api\Data\CustomerInterface`
  Use this field and `\Magento\Customer\Api\CustomerRepositoryInterface` to load/save Vertex Customer Codes


## Testing

### Running unit tests

* Run `composer install` in the module directory.
* Run `composer test:unit:opensource` for most unit tests.
* Run `composer test:unit:commerce` for tests that rely on Magento Commerce features. These tests require `magento/module-gift-wrapping`.

### Running functional tests

* Install Magento 2.
* Install Vertex Tax Links for Magento 2.
* Pre-configure Vertex Tax Links for Magento 2. (Functional tests assume tax will be charged to Pennsylvania addresses.)
* Run `composer install` in the module directory.
* Update `Test/.env` based on `Test/.env.example`, if necessary.
* Run `composer test:functional`.


## Architecture

![Vertex Tax Links for Magento 2 ArchitectureI](https://i.imgur.com/kYmWfAi.png)

The core functionality of Vertex Tax Links for Magento 2 intercepts the tax request from the Magento software and relays it through a Vertex Tax Links-compatible service, such as Vertex Cloud.

This module uses a variety of models to convert a Magento Quote, Order, or Invoice object to a compatible Vertex request object.

### Cart estimation / Order placed

The Cart Estimation and Order Placed functionalities use the `Vertex\Tax\Model\Request\Type\QuotationRequest` class and its helper models located in `Vertex\Tax\Model\Request` and `Vertex\Tax\Model\TaxQuote`.  This process is automatically triggered by the `Vertex\Tax\Model\Plugin\CalculatorFactoryPlugin`, which is called whenever Magento attempts to calculate Tax.

After retrieving the taxes eligible for the order, the plugin places this information in the global Magento registry so that it can be called to calculate the taxes per line-item.  This practice reduces the number of requests to the Vertex API server and ensures store speed.

These taxes are then re-applied when calling `GiftWrappingTaxPlugin`, `SubtotalPlugin`, and `TotalPlugin`.

### Invoice / Creditmemo

Vertex Tax Links for Magento 2 issues a request to the Vertex API server to record an entry in the Vertex Tax Log during the following events:

* The Magento invoice procedure or an order status change (depending on how Vertex is configured)
* The issuance of a credit memo

These events are triggered by the observers `CreditMemoObserver`, `InvoiceSavedAfterObserver`, and/or `OrderSavedAfterObserver`. They all use the `Vertex\Tax\Model\TaxInvoice` and the `Vertex\Tax\Model\TaxInvoice\` series of classes to compile requests and responses.

### Tax area requests

The Admin panel verifies the connector is properly configured. A tax area request validates an address and/or determines the jurisdictions imposing sales & use taxes on an address.  They are controlled by the `Vertex\Tax\Model\TaxArea\` series of classes. 

### Request/Response logging

Vertex requests and responses are logged, by default to the database, by using the repository class `Vertex\Tax\Model\Repository\LogEntryRepository`, the data class/model `Vertex\Tax\Model\Data\LogEntry`, and the resource models `Vertex\Tax\Model\ResourceModel\LogEntry` and `Vertex\Tax\Model\ResourceModel\LogEntry\Collection`.

To replace or interact with this log, please utilize the `@api` annotated service contracts present in the `Vertex\Tax\Api` namespace.

### Future changes

In the near future, we plan to remove the various classes for translating between Magento objects and Vertex requests/responses to a library that will be required by this module.  This library will use data objects with getters and setters to abstract away the implementation details of this module.
