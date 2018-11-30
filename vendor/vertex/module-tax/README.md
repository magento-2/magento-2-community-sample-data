# Vertex Tax Links for Magento 2

The Vertex Tax Links for Magento 2 module allows merchants to utilize Vertex Tax Links or Vertex Cloud to provide the relevant tax calculations for US-based sales.

## Public API

Vertex Tax Links provides a few key places for integration and customization.  These are all marked with the `@api` annotation.

**Note**: Anything not marked with the `@api` annotation may be modified or removed in future versions, unless specified in below list of public API access points

* `\Vertex\Tax\Api\LogEntryRepositoryInterface` 
  * Use this class to interface with the default Log Entry datastore (the database), or provide a preference to store 
logs in an external system.
* `\Vertex\Tax\Api\Data\LogEntryInterface`  
  * This interface represents a Log Entry.
* `\Vertex\Tax\Api\Data\LogEntrySearchResultsInterface`  
  * This interface represents a list of results for a search against the `LogEntryRepositoryInterface`
* `\Vertex\Tax\Api\InvoiceInterface::record`  
  * Use this method to record an invoice in Vertex  
  * Intercept this method to modify the Invoice's request or response  
* `\Vertex\Tax\Api\QuoteInterface::request`  
  * Use this method to request a tax quotation from Vertex  
  * Intercept this method to modify the quotation's request or response
* `\Vertex\Tax\Api\TaxAreaLookupInterface::lookup`
  * Use this method to perform address validation or look up possible tax areas for an address
* `vertex_customer_code` extension attribute on `\Magento\Customer\Api\Data\CustomerInterface`  
  Use this field and `\Magento\Customer\Api\CustomerRepositoryInterface` to load/save Vertex Customer Codes


## Testing

Vertex Tax Links comes with Unit, Integration, and Functional Acceptance tests.

The unit and integration tests may be ran from within Magento as part of Magento's unit and integration test suites.

### Functional Acceptance Tests

Vertex comes with functional acceptance tests that utilize the Magento Functional Testing Framework.

Running these tests requires MFTF to be setup.  Please refer to [Introduction to the Magento Functional Testing 
Framework](https://devdocs.magento.com/guides/v2.2/magento-functional-testing-framework/release-2/introduction.html) 
for more information on setting up this environment.

After an MFTF environment is setup, the following steps must be taken:

#### Vertex Settings

Vertex Tax Links must be configured with a Trusted ID and company address that are already set up in Vertex to 
calculate and record taxes in Pennsylvania.

#### MFTF Settings

The path of the Test/Mftf folder within Vertex must be specified in the MFTF environment variable 
`CUSTOM_MODULE_PATHS`.  This variable should contain the full path on the system for the Mftf directory.

For example: (where `/var/www/example.org` is the root of a Magento 2 installation)

> `/var/www/example.org/vendor/vertex/module-tax/Test/Mftf`

Once configuration is complete, the tests may be run as specified in [Step 7 of the MFTF Getting Started notes](https://devdocs.magento.com/guides/v2.2/magento-functional-testing-framework/release-2/getting-started.html#step-7-run-tests).

## Architecture

![Vertex Tax Links for Magento 2 ArchitectureI](https://i.imgur.com/kYmWfAi.png)

The core functionality of Vertex Tax Links for Magento 2 intercepts the tax request from the Magento software and relays it through a Vertex Tax Links-compatible service, such as Vertex Cloud.

This module uses a variety of models to convert a Magento Quote, Order, or Invoice object to a compatible Vertex request object.

### Cart estimation / Order placed

The Cart Estimation and Order Placed functionalities use the `Vertex\Tax\Model\Api\Data\QuotationRequest` class and its helper models located in `Vertex\Tax\Model\Request`, `Vertex\Tax\Model\TaxQuote` and `Vertex\Tax\Model\Api`.  

This process is automatically triggered by the `Vertex\Tax\Model\Plugin\QuoteTaxCollectorPlugin`, which is called  whenever Magento attempts to calculate Tax.

After retrieving the taxes eligible for the order, the plugin places this information in a cache so that it can be called to calculate the taxes per line-item.  This practice reduces the number of requests to the Vertex API server and ensures store speed.

These taxes are then re-applied when calling `SubtotalPlugin`.

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
