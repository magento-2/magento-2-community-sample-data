<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Api;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Base class to generate API configuration
 */
interface BuilderInterface
{
    const GENERATE_TYPE_CREATE        = 'create';
    const GENERATE_TYPE_UPDATE        = 'update';
    const GENERATE_TYPE_PLACE         = 'place';
    const GENERATE_TYPE_CLIENT_UPDATE = 'client_update';

    /**
     * Generate order body
     *
     * @param string $type
     * @return $this
     * @throws \Klarna\Core\Exception
     */
    public function generateRequest($type = self::GENERATE_TYPE_CREATE);

    /**
     * Collect order lines
     *
     * @param StoreInterface $store
     * @return $this
     */
    public function collectOrderLines(StoreInterface $store);

    /**
     * Get totals collector model
     *
     * @return OrderLineInterface
     */
    public function getOrderLinesCollector();

    /**
     * Get the object used to generate request
     *
     * @return \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote
     */
    public function getObject();

    /**
     * Set the object used to generate request
     *
     * @param \Magento\Sales\Model\AbstractModel|\Magento\Quote\Api\Data\CartInterface $object
     *
     * @return $this
     */
    public function setObject($object);

    /**
     * Get request
     *
     * @return array
     */
    public function getRequest();

    /**
     * Set generated request
     *
     * @param array  $request
     * @param string $type
     *
     * @return $this
     */
    public function setRequest(array $request, $type = self::GENERATE_TYPE_CREATE);

    /**
     * Get order lines as array
     *
     * @param StoreInterface $store
     * @param bool           $orderItemsOnly
     *
     * @return array
     */
    public function getOrderLines(StoreInterface $store, $orderItemsOnly = false);

    /**
     * Add an order line
     *
     * @param array $orderLine
     *
     * @return $this
     */
    public function addOrderLine(array $orderLine);

    /**
     * Remove all order lines
     *
     * @return $this
     */
    public function resetOrderLines();

    /**
     * @param $items
     * @return $this
     */
    public function setItems($items);

    /**
     * @return array
     */
    public function getItems();
}
