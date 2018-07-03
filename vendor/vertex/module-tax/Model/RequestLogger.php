<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Vertex\Tax\Api\Data\LogEntryInterface;
use Vertex\Tax\Api\Data\LogEntryInterfaceFactory;
use Vertex\Tax\Api\LogEntryRepositoryInterface;

/**
 * Performs all the actions necessary for logging a request
 */
class RequestLogger
{
    /** @var LogEntryRepositoryInterface */
    private $repository;

    /** @var LogEntryInterfaceFactory */
    private $factory;

    /** @var DateTime */
    private $dateTime;

    /** @var DomDocumentFactory */
    private $documentFactory;

    /**
     * @param LogEntryRepositoryInterface $repository
     * @param LogEntryInterfaceFactory $logEntryFactory
     * @param DateTime $dateTime
     * @param DomDocumentFactory $documentFactory
     */
    public function __construct(
        LogEntryRepositoryInterface $repository,
        LogEntryInterfaceFactory $logEntryFactory,
        DateTime $dateTime,
        DomDocumentFactory $documentFactory
    ) {
        $this->repository = $repository;
        $this->factory = $logEntryFactory;
        $this->dateTime = $dateTime;
        $this->documentFactory = $documentFactory;
    }

    /**
     * Log a Request
     *
     * @param string $type
     * @param string $requestXml
     * @param string $responseXml
     * @param int|array $totalTax
     * @param int $taxAreaId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function log($type, $requestXml, $responseXml, $totalTax = 0, $taxAreaId = 0)
    {
        if (is_array($totalTax)) {
            $totalTax = $totalTax['_'];
        }
        /** @var LogEntryInterface $logEntry */
        $logEntry = $this->factory->create();
        $timestamp = $this->dateTime->date('Y-m-d H:i:s');
        $logEntry->setType($type);
        $logEntry->setDate($timestamp);

        $requestXml = $this->formatXml($requestXml);
        $responseXml = $this->formatXml($responseXml);

        $logEntry->setTotalTax($totalTax);
        $logEntry->setRequestXml($requestXml);
        $logEntry->setResponseXml($responseXml);
        $logEntry->setTaxAreaId($taxAreaId);
        $this->addResponseDataToLogEntry($logEntry, $responseXml);
        $this->repository->save($logEntry);
    }

    /**
     * Format a string of XML
     *
     * @param string $xml
     * @return string
     */
    private function formatXml($xml)
    {
        if (empty($xml)) {
            return '';
        }

        $dom = $this->documentFactory->create();

        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * Add data from the response XML to the LogEntry
     *
     * @param LogEntryInterface $logEntry
     * @param string $responseXml
     * @return LogEntryInterface
     */
    private function addResponseDataToLogEntry(LogEntryInterface $logEntry, $responseXml)
    {
        $dom = $this->documentFactory->create();

        if (!empty($responseXml)) {
            $dom->loadXML($responseXml);

            $totalNode = $dom->getElementsByTagName('Total');
            $subtotalNode = $dom->getElementsByTagName('SubTotal');
            $lookupResultNode = $dom->getElementsByTagName('Status');
            $addressLookupFaultNode = $dom->getElementsByTagName('exceptionType');
            $total = $totalNode->length > 0 ? $totalNode->item(0)->nodeValue : 0;
            $subtotal = $subtotalNode->length > 0 ? $subtotalNode->item(0)->nodeValue : 0;

            $lookupResult = '';
            if ($lookupResultNode->length > 0) {
                $lookupResult = $lookupResultNode->item(0)->getAttribute('lookupResult');
            } elseif ($addressLookupFaultNode->length > 0) {
                $lookupResult = $addressLookupFaultNode->item(0)->nodeValue;
            }

            $logEntry->setTotal($total);
            $logEntry->setSubTotal($subtotal);
            $logEntry->setLookupResult($lookupResult);
        }

        return $logEntry;
    }
}
