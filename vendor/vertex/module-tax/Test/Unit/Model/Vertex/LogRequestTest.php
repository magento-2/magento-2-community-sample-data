<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Vertex;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Vertex\Tax\Api\Data\LogEntryInterface;
use Vertex\Tax\Api\Data\LogEntryInterfaceFactory;
use Vertex\Tax\Api\LogEntryRepositoryInterface;
use Vertex\Tax\Model\ApiClient;
use Vertex\Tax\Model\DomDocumentFactory;
use Vertex\Tax\Model\RequestLogger;
use Vertex\Tax\Test\Unit\TestCase;

class LogRequestTest extends TestCase
{
    const DATE = '1991-08-06 12:00:00';
    const OBJECT_ID = '42';
    const REQUEST_XML = '<request/>';
    const REQUEST_XML_FORMATTED = "<?xml version=\"1.0\"?>\n<request/>\n";
    const RESPONSE_EXCEPTIONTYPE = 'SOME EXCEPTION';
    const RESPONSE_LOOKUP_RESULT = 'RESULT OK';
    const RESPONSE_SUBTOTAL = '4.20';
    const RESPONSE_TOTAL = '5.25';
    const RESPONSE_TOTAL_TAX = '1.05';
    const RESPONSE_XML = '<QuotationResponse><TotalTax>1.05</TotalTax><Total>5.25</Total><SubTotal>4.20</SubTotal>'
    . '<Status lookupResult="RESULT OK"/></QuotationResponse>';
    const RESPONSE_XML_EXCEPTIONTYPE = '<response><exceptionType>SOME EXCEPTION</exceptionType></response>';
    const RESPONSE_XML_FORMATTED = <<<XML
<?xml version="1.0"?>
<QuotationResponse>
  <TotalTax>1.05</TotalTax>
  <Total>5.25</Total>
  <SubTotal>4.20</SubTotal>
  <Status lookupResult="RESULT OK"/>
</QuotationResponse>\n
XML;
    const SOURCE_PATH = 'source_path';
    const TAX_AREA_ID = '3.14';
    private $dateTimeFactory;

    public function setUp()
    {
        parent::setUp();

        $dateTimeMock = $this->createPartialMock(DateTime::class, ['date']);
        $dateTimeMock->expects($this->any())
            ->method('date')
            ->willReturn(static::DATE);

        $this->dateTimeFactory = $this->createMock(DateTimeFactory::class);
        $this->dateTimeFactory->expects($this->any())
            ->method('create')
            ->willReturn($dateTimeMock);
    }

    public function testHappyLogRequest()
    {
        $type = 'third_type';

        $logEntry = $this->createMock(LogEntryInterface::class);

        // Test that factory is used to generate LogEntry
        $logEntryFactory = $this->createMock(LogEntryInterfaceFactory::class);
        $logEntryFactory->expects($this->once())
            ->method('create')
            ->willReturn($logEntry);

        // Test that repository is used to save LogEntry
        $logEntryRepository = $this->createMock(LogEntryRepositoryInterface::class);
        $logEntryRepository->expects($this->once())
            ->method('save')
            ->with($logEntry);

        // Test that Type is properly set as passed in parameter
        $logEntry->expects($this->once())
            ->method('setType')
            ->with($type)
            ->willReturnSelf();

        // Test that Date is properly set as the date from the DateTimeFactory's DateTime
        $logEntry->expects($this->once())
            ->method('setDate')
            ->with(static::DATE)
            ->willReturnSelf();

        // Test that Request XML is formatted
        $logEntry->expects($this->once())
            ->method('setRequestXml')
            ->with(static::REQUEST_XML_FORMATTED);

        // Test that Response XML is formatted
        $logEntry->expects($this->once())
            ->method('setResponseXml')
            ->with(static::RESPONSE_XML_FORMATTED);

        // Test that Total Tax is set from XML
        $logEntry->expects($this->once())
            ->method('setTotalTax')
            ->with(static::RESPONSE_TOTAL_TAX);

        // Test that total is set from XML
        $logEntry->expects($this->once())
            ->method('setTotal')
            ->with(static::RESPONSE_TOTAL);

        // Test that subtotal is set from XML
        $logEntry->expects($this->once())
            ->method('setSubTotal')
            ->with(static::RESPONSE_SUBTOTAL);

        // Test that lookup result is set from XML
        $logEntry->expects($this->once())
            ->method('setLookupResult')
            ->with(static::RESPONSE_LOOKUP_RESULT);

        $requestLogger = $this->getObject(
            RequestLogger::class,
            [
                'repository' => $logEntryRepository,
                'logEntryFactory' => $logEntryFactory,
                'dateTime' => $this->dateTimeFactory->create(),
                'documentFactory' => $this->getObject(DomDocumentFactory::class),
            ]
        );

        $vertex = $this->getObject(
            ApiClient::class,
            [
                'requestLogger' => $requestLogger,
            ]
        );

        $this->invokeInaccessibleMethod(
            $vertex,
            'logRequest',
            $type,
            static::REQUEST_XML,
            static::RESPONSE_XML,
            ['_' => static::RESPONSE_TOTAL_TAX],
            static::TAX_AREA_ID
        );
    }
}
