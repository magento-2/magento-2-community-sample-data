<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice;

use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;
use Temando\Shipping\Model\OrderInterface;

/**
 * Temando Order Action Locator
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderActionLocator
{
    /**
     * No API call
     */
    const ACTION_NONE= 'none';

    /**
     * Retrieve rates
     */
    const ACTION_QUALIFY = 'qualify';

    /**
     * Retrieve collection points with rates
     */
    const ACTION_QUOTE_COLLECTION_POINTS = 'quote_collection_points';

    /**
     * Manifest order
     */
    const ACTION_PERSIST = 'persist';

    /**
     * Manifest order and allocate to shipment(s)
     */
    const ACTION_ALLOCATE = 'allocate';

    /**
     * Update order that was already persisted at platform
     */
    const ACTION_UPDATE = 'update';

    /**
     * Update placed order that was already created at the platform.
     *
     * @return string
     */
    private function getUpdateOrderAction()
    {
        return self::ACTION_UPDATE;
    }

    /**
     * Manifest new order at the platform, optionally with shipment allocation.
     *
     * @param string $orderStatus
     * @return string
     */
    private function getCreateOrderAction($orderStatus)
    {
        // might be replaced by config setting in the future.
        $isOrderAllocationEnabled = true;

        if (!$isOrderAllocationEnabled) {
            // allocation disabled in config, regular persist action
            return self::ACTION_PERSIST;
        }

        if ($orderStatus === OrderInterface::STATUS_AWAITING_PAYMENT) {
            // order not ready for allocation, regular persist action
            return self::ACTION_PERSIST;
        }

        return self::ACTION_ALLOCATE;
    }

    /**
     * Obtain rates, optionally for collection points.
     *
     * @param OrderInterface $order
     * @return string
     */
    private function getQualifyOrderAction(OrderInterface $order)
    {
        $searchRequest = $order->getCollectionPointSearchRequest();

        // collection point delivery chosen, no search triggered yet
        $isCollectionPointDeliveryOption = $searchRequest->isPending();
        if ($isCollectionPointDeliveryOption) {
            return self::ACTION_NONE;
        }

        // collection point search request exists
        $isCollectionPointSearch = ($searchRequest->getPostcode() && $searchRequest->getCountryId());

        // a collection point was selected for quoting
        $isCollectionPointQualify = (bool) $order->getCollectionPoint()->getCollectionPointId();

        if (!$isCollectionPointQualify && $isCollectionPointSearch) {
            return self::ACTION_QUOTE_COLLECTION_POINTS;
        }

        return self::ACTION_QUALIFY;
    }

    /**
     * Determine appropriate API action for the given order.
     *
     * Possible actions:
     * - Qualify
     * -- Get quotes for current cart
     * -- Get quotes for current cart, including possible collection points
     * - Create
     * -- Persist placed order at the platform
     * -- Persist placed order at the platform and allocate shipments
     * - Update
     * -- Modify placed order that already exists at the platform
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getOrderAction(OrderInterface $order)
    {
        $platformOrderId = $order->getOrderId();
        $salesOrderId = $order->getSourceId();

        if (!$platformOrderId && !$salesOrderId) {
            // quoting, optionally with collection point search
            return $this->getQualifyOrderAction($order);
        }

        if (!$platformOrderId && $salesOrderId) {
            // manifest, optionally with shipment allocation
            $orderStatus = $order->getStatus();
            return $this->getCreateOrderAction($orderStatus);
        }

        if ($platformOrderId && $salesOrderId) {
            // update placed order
            return $this->getUpdateOrderAction();
        }

        return '';
    }
}
