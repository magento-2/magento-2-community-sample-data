# Vertex API Library

The Vertex API Library provides data models for storing information related to Vertex requests and responses, mappers
 for converting these data models to a format compatible with the PHP SOAP extension, and services for calling the 
 Vertex API endpoints.
 
## Public API

The Vertex API Library provides backwards-compatibility in line with [Magento 2 versioning constraints](https://devdocs.magento.com/guides/v2.2/extension-dev-guide/versioning/dependencies.html).

As such, classes that can be relied upon are marked with an `@api` annotation.

* Interfaces in `Vertex\Data`
  * These are interfaces representing data models.  They are used for assembling requests and responses to the Vertex
    SOAP API.
* Exceptions in `Vertex\Exception`
  * All exceptions may be thrown by Service interfaces.  There are three primary exception types:
    * `ConfigurationException` - thrown when the configuration is known to be invalid
    * `ValidationException` - thrown when mapping a request has failed due to known value constraints
    * `ApiException` - thrown when the API returns an error.  This exception is subclassed for more specific errors
      * `AuthenticationException` - thrown when Vertex responds that authentication data is incorrect
      * `ConnectionFailureException` - thrown when the library fails to connect to the Vertex SOAP API
* Interfaces in `Vertex\Mapper`
  * These are interfaces utilized for providing contracts for mapping a Vertex data model to a SOAP compatible 
    format or building a SOAP compatible format into a Vertex data model.
* `Vertex\Mapper\MapperFactory` provides methods for retrieving a mapper based on the class it should be mapping and 
  the version of Vertex it needs to map for.  It is configured through the constructor
* Interfaces in `Vertex\Services\*\`
  * These are interfaces representing request and response data models.
* `Vertex\Services\Invoice` records an invoice in the Vertex Tax Log
* `Vertex\Services\Quote` requests a quotation for tax costs
* `Vertex\Services\TaxAreaLookup` attempts to validate an address and provide relevant jurisdictions where is taxed.
* Classes in `Vertex\Utility\FaultConverter` determine if a SoapFault matches their conditions for being thrown and 
  return the proper exception if so
* `Vertex\Utility\SoapClientFactory` contains the default options for communicating with the Vertex SOAP API
* `Vertex\Utility\SoapFaultConverterFactory` creates a `PooledSoapFaultConverter` for converting any SoapFault into 
  one of the library's exceptions
* `Vertex\Utility\SoapFaultConverterInterface` provides the contract all SoapFault Converters must adhere to in order
  to be useful.
* `Vertex\Utility\VersionDeterminer` provides the version of the Vertex API based on the WSDL URL

## Testing

The Vertex API Library comes with Unit and Integration tests.

* `composer test:unit` runs the unit test suite
* `composer test:integration` runs the integration test suite

## Architecture

![](https://i.imgur.com/93vuab6.png)

The Vertex API Library expects that a consumer of the library will assemble the Request and data models necessary for
 it by hand.  The consumer will then call one of the Service methods.
 
The service method will utilize `MapperFactory` to retrieve the mapper for its Request Interface, and then use the 
 result with a SoapClient created through the use of the `SoapClientFactory`.
 
If the response received from Vertex is an Exception (SoapFault), the service will run the fault through the 
 SoapFault converter returned from `SoapFaultConverterFactory`.  If this converter returns an exception, the service 
 will throw it.  If it does not, the service will throw an `ApiException`.
 
If the response received is not an Exception, the service will run it through the mapper for its Response Interface 
 and return this result to the caller.  
