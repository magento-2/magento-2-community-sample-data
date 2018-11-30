<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Vertex;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Api\Data\LogEntryInterface;
use Vertex\Tax\Api\Data\LogEntryInterfaceFactory;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\DomDocumentFactory;
use Vertex\Tax\Model\ApiClient\ObjectConverter;
use Vertex\Tax\Model\RequestLogger;
use Vertex\Tax\Test\Unit\TestCase;
use Vertex\Utility\SoapClientFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendApiRequestTest extends TestCase
{
    const OBJECT_ID = '3.14';
    const STORE_ID = '4.18';

    const REQUEST_XML = '<request />';
    const RESPONSE_XML = '<response />';
    const TYPE = 'some_type';
    const TYPE_LOOKUP = 'tax_area_lookup';
    const ORDER = null;
    const DATE = '1991-08-06 12:00:00';

    const VALIDATION_FUNCTION = 'LookupTaxAreas60';
    const CALCULATION_FUNCTION = 'CalculateTax60';
    const VERTEX_HOST = 'vertex_host';
    const VERTEX_ADDRESS_HOST = 'vertex_address_host';

    private $mockConfig;
    private $dateTimeFactory;
    private $storeManagerMock;

    private $defaultRequest = [];

    protected function setUp()
    {
        parent::setUp();

        $this->mockConfig = $this->createPartialMock(
            Config::class,
            ['getValidationFunction', 'getCalculationFunction', 'getVertexHost', 'getVertexAddressHost']
        );
        $this->mockConfig->method('getValidationFunction')
            ->willReturn(static::VALIDATION_FUNCTION);
        $this->mockConfig->method('getCalculationFunction')
            ->willReturn(static::CALCULATION_FUNCTION);
        $this->mockConfig->method('getVertexHost')
            ->willReturn(static::VERTEX_HOST);
        $this->mockConfig->method('getVertexAddressHost')
            ->willReturn(static::VERTEX_ADDRESS_HOST);

        $dateTimeMock = $this->createPartialMock(DateTime::class, ['date']);
        $dateTimeMock->expects($this->any())
            ->method('date')
            ->willReturn(static::DATE);

        $this->dateTimeFactory = $this->createMock(DateTimeFactory::class);
        $this->dateTimeFactory->expects($this->any())
            ->method('create')
            ->willReturn($dateTimeMock);

        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')
            ->willReturn(static::STORE_ID);

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->storeManagerMock->method('getStore')
            ->willReturn($storeMock);
    }

    public function testTaxAreaLookupCallsValidationAndLogs()
    {
        $mockSoapClient = $this->createPartialMock(
            \SoapClient::class,
            [static::VALIDATION_FUNCTION, static::CALCULATION_FUNCTION, '__getLastRequest', '__getLastResponse']
        );
        $mockSoapClient->method('__getLastRequest')
            ->willReturn(static::REQUEST_XML);
        $mockSoapClient->method('__getLastResponse')
            ->willReturn(static::RESPONSE_XML);
        $mockSoapClient->expects($this->once())
            ->method(static::VALIDATION_FUNCTION)
            ->willReturn(['key0' => 'something', 'key1' => 'result']);

        $mockSoapClientFactory = $this->createPartialMock(SoapClientFactory::class, ['create']);
        $mockSoapClientFactory->method('create')
            ->willReturn($mockSoapClient);

        $logEntryMock = $this->createMock(LogEntryInterface::class);
        $logEntryFactoryMock = $this->createPartialMock(LogEntryInterfaceFactory::class, ['create']);
        $logEntryFactoryMock->method('create')
            ->willReturn($logEntryMock);

        // Test logged type is type passed to method
        $logEntryMock->expects($this->once())
            ->method('setType')
            ->with(static::TYPE_LOOKUP);

        $requestLogger = $this->getObject(
            RequestLogger::class,
            [
                'logEntryFactory' => $logEntryFactoryMock,
                'dateTime' => $this->dateTimeFactory->create(),
                'documentFactory' => $this->getObject(DomDocumentFactory::class),
            ]
        );

        $vertex = $this->getObject(
            ApiClient::class,
            [
                'config' => $this->mockConfig,
                'soapClientFactory' => $mockSoapClientFactory,
                'storeManager' => $this->storeManagerMock,
                'requestLogger' => $requestLogger,
                'objectConverter' => $this->getObject(ObjectConverter::class),
            ]
        );
        $vertex->sendApiRequest($this->defaultRequest, static::TYPE_LOOKUP, static::ORDER);
    }

    public function testNonSoapExceptionDuringRequestLogs()
    {
        $mockSoapClient = $this->createMock(\SoapClient::class);

        $mockSoapClientFactory = $this->createPartialMock(SoapClientFactory::class, ['create']);
        $mockSoapClientFactory->method('create')
            ->willReturn($mockSoapClient);

        // Test logged through Magento
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('critical');

        // Ensure no attempt is made to log to database
        $logFactory = $this->createMock(LogEntryInterfaceFactory::class);
        $logFactory->expects($this->never())
            ->method('create');

        /** @var ApiClient $vertex */
        $vertex = $this->getObject(
            ApiClient::class,
            [
                'logger' => $loggerMock,
                'soapClientFactory' => $mockSoapClientFactory,
                'storeManager' => $this->storeManagerMock,
                'logEntryFactory' => $logFactory,
            ]
        );

        $vertex->sendApiRequest($this->defaultRequest, 'invalid_type', static::ORDER);
    }

    public function testErrorLoggingIsLogged()
    {
        $requestException = new \SoapFault('test', 'Exception during Request');
        $loggingException = new \Exception('Exception during Logging');

        $mockSoapClient = $this->createPartialMock(
            \SoapClient::class,
            [static::VALIDATION_FUNCTION, static::CALCULATION_FUNCTION, '__getLastRequest', '__getLastResponse']
        );
        $mockSoapClient->method('__getLastRequest')
            ->willReturn(static::REQUEST_XML);
        $mockSoapClient->method('__getLastResponse')
            ->willReturn(static::RESPONSE_XML);
        $mockSoapClient->expects($this->once())
            ->method(static::VALIDATION_FUNCTION)
            ->willReturn($requestException);

        $mockSoapClientFactory = $this->createPartialMock(SoapClientFactory::class, ['create']);
        $mockSoapClientFactory->method('create')
            ->willReturn($mockSoapClient);

        $requestLoggerMock = $this->createPartialMock(RequestLogger::class, ['log']);
        $requestLoggerMock->method('log')
            ->willThrowException($loggingException);

        // Test logged through Magento
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->exactly(2))
            ->method('critical');

        $vertex = $this->getObject(
            ApiClient::class,
            [
                'config' => $this->mockConfig,
                'soapClientFactory' => $mockSoapClientFactory,
                'dateTimeFactory' => $this->dateTimeFactory,
                'requestLogger' => $requestLoggerMock,
                'storeManager' => $this->storeManagerMock,
                'logger' => $loggerMock,
            ]
        );
        $vertex->sendApiRequest($this->defaultRequest, static::TYPE_LOOKUP, static::ORDER);
    }
}
