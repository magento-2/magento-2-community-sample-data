<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Delivery\OrderPickupLocationInterface;

/**
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface OrderPickupLocationRepositoryInterface
{
    /**
     * Load pickup location by shipping address id.
     *
     * @param int $addressId
     * @return OrderPickupLocationInterface
     * @throws NoSuchEntityException
     */
    public function get($addressId);

    /**
     * @param OrderPickupLocationInterface $collectionPoint
     * @return OrderPickupLocationInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderPickupLocationInterface $collectionPoint);
}
