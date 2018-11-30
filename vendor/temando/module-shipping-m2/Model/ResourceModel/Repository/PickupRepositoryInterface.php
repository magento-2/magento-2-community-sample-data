<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use Temando\Shipping\Model\PickupInterface;

/**
 * Temando Pickup Fulfillment Repository Interface.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface PickupRepositoryInterface
{
    /**
     * @param string $pickupId
     * @return \Temando\Shipping\Model\PickupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pickupId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Temando\Shipping\Model\PickupInterface[]
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param \Temando\Shipping\Model\PickupInterface $pickup
     * @return \Temando\Shipping\Model\PickupInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(PickupInterface $pickup);
}
