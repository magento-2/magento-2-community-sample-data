<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Data;

use Magento\Framework\Model\AbstractModel;
use Vertex\Tax\Api\Data\LogEntryInterface;
use Vertex\Tax\Model\ResourceModel\LogEntry as ResourceModel;

/**
 * Data model for a Log Entry
 */
class LogEntry extends AbstractModel implements LogEntryInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->getData(static::FIELD_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(static::FIELD_TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getCartId()
    {
        return $this->getData(static::FIELD_CART_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCartId($cartId)
    {
        return $this->setData(static::FIELD_CART_ID, $cartId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(static::FIELD_ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(static::FIELD_ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getTotalTax()
    {
        return $this->getData(static::FIELD_TOTAL_TAX);
    }

    /**
     * @inheritdoc
     */
    public function setTotalTax($totalTax)
    {
        return $this->setData(static::FIELD_TOTAL_TAX, $totalTax);
    }

    /**
     * @inheritdoc
     */
    public function getSourcePath()
    {
        return $this->getData(static::FIELD_SOURCE_PATH);
    }

    /**
     * @inheritdoc
     */
    public function setSourcePath($sourcePath)
    {
        return $this->setData(static::FIELD_SOURCE_PATH, $sourcePath);
    }

    /**
     * @inheritdoc
     */
    public function getTaxAreaId()
    {
        return $this->getData(static::FIELD_TAX_AREA_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTaxAreaId($taxAreaId)
    {
        return $this->setData(static::FIELD_TAX_AREA_ID, $taxAreaId);
    }

    /**
     * @inheritdoc
     */
    public function getSubTotal()
    {
        return $this->getData(static::FIELD_SUBTOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setSubTotal($subtotal)
    {
        return $this->setData(static::FIELD_SUBTOTAL, $subtotal);
    }

    /**
     * @inheritdoc
     */
    public function getTotal()
    {
        return $this->getData(static::FIELD_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setTotal($total)
    {
        return $this->setData(static::FIELD_TOTAL, $total);
    }

    /**
     * @inheritdoc
     */
    public function getLookupResult()
    {
        return $this->getData(static::FIELD_LOOKUP_RESULT);
    }

    /**
     * @inheritdoc
     */
    public function setLookupResult($lookupResult)
    {
        return $this->setData(static::FIELD_LOOKUP_RESULT, $lookupResult);
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->getData(static::FIELD_REQUEST_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setDate($requestDate)
    {
        return $this->setData(static::FIELD_REQUEST_DATE, $requestDate);
    }

    /**
     * @inheritdoc
     */
    public function getRequestXml()
    {
        return $this->getData(static::FIELD_REQUEST_XML);
    }

    /**
     * @inheritdoc
     */
    public function setRequestXml($requestXml)
    {
        return $this->setData(static::FIELD_REQUEST_XML, $requestXml);
    }

    /**
     * @inheritdoc
     */
    public function getResponseXml()
    {
        return $this->getData(static::FIELD_RESPONSE_XML);
    }

    /**
     * @inheritdoc
     */
    public function setResponseXml($responseXml)
    {
        return $this->setData(static::FIELD_RESPONSE_XML, $responseXml);
    }
}
