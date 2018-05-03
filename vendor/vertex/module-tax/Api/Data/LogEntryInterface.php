<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Api\Data;

/**
 * Data model representing an entry in the Vertex API Log
 *
 * @api
 */
interface LogEntryInterface
{
    const FIELD_ID = 'request_id';
    const FIELD_TYPE = 'request_type';
    const FIELD_CART_ID = 'quote_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_TOTAL_TAX = 'total_tax';
    const FIELD_SOURCE_PATH = 'source_path';
    const FIELD_TAX_AREA_ID = 'tax_area_id';
    const FIELD_SUBTOTAL = 'sub_total';
    const FIELD_TOTAL = 'total';
    const FIELD_LOOKUP_RESULT = 'lookup_result';
    const FIELD_REQUEST_DATE = 'request_date';
    const FIELD_REQUEST_XML = 'request_xml';
    const FIELD_RESPONSE_XML = 'response_xml';

    /**
     * Retrieve unique identifier for the Log Entry
     *
     * @return int
     */
    public function getId();

    /**
     * Set unique identifier for the Log Entry
     *
     * @param int $requestId
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setId($requestId);

    /**
     * Get the type of request
     *
     * Typically one of quote, invoice, tax_area_lookup or creditmemo
     *
     * @return string
     */
    public function getType();

    /**
     * Set the type of request
     *
     * Typically one of quote, invoice, tax_area_lookup or creditmemo
     *
     * @param string $type
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setType($type);

    /**
     * Get the ID of the Order the request was made for
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set the ID of the Order the request was made for
     *
     * @param int $orderId
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setOrderId($orderId);

    /**
     * Get the total amount of tax calculated by the request
     *
     * @return float
     */
    public function getTotalTax();

    /**
     * Set the total amount of tax calculated by the request
     *
     * @param float $totalTax
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setTotalTax($totalTax);

    /**
     * Get the Tax Area ID calculated by the request
     *
     * @return int
     */
    public function getTaxAreaId();

    /**
     * Set the Tax Area ID calculated by the request
     *
     * @param int $taxAreaId
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setTaxAreaId($taxAreaId);

    /**
     * Get the total of the request before taxes
     *
     * @return float
     */
    public function getSubTotal();

    /**
     * Set the total of the request before taxes
     *
     * @param float $subtotal
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setSubTotal($subtotal);

    /**
     * Get the total of the request after taxes
     *
     * @return float
     */
    public function getTotal();

    /**
     * Set the total of the request after taxes
     *
     * @param float $total
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setTotal($total);

    /**
     * Get the result of the lookup
     *
     * Typically empty, the string "NORMAL" or a SOAP Exception
     *
     * @return string
     */
    public function getLookupResult();

    /**
     * Set the result of the lookup
     *
     * Typically empty, the string "NORMAL" or a SOAP Exception
     *
     * @param string $lookupResult
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setLookupResult($lookupResult);

    /**
     * Get the date of the request
     *
     * @return string
     */
    public function getDate();

    /**
     * Set the date of the request
     *
     * @param string $requestDate Date in format of Y-m-d H:i:s
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setDate($requestDate);

    /**
     * Get the XML sent to the Vertex API
     *
     * @return string
     */
    public function getRequestXml();

    /**
     * Set the XML sent to the Vertex API
     *
     * @param string $requestXml
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setRequestXml($requestXml);

    /**
     * Get the XML response received from the Vertex API
     *
     * @return string
     */
    public function getResponseXml();

    /**
     * Set the XML response received from the Vertex API
     *
     * @param string $responseXml
     * @return \Vertex\Tax\Api\Data\LogEntryInterface
     */
    public function setResponseXml($responseXml);
}
