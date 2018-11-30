<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Delivery\PickupLocationSearchRequestInterface;

/**
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface PickupLocationSearchRepositoryInterface
{
    /**
     * @param int $quoteAddressId
     * @return PickupLocationSearchRequestInterface
     * @throws NoSuchEntityException
     */
    public function get($quoteAddressId);

    /**
     * @param PickupLocationSearchRequestInterface $searchRequest
     * @return PickupLocationSearchRequestInterface
     * @throws CouldNotSaveException
     */
    public function save($searchRequest);

    /**
     * @param int $quoteAddressId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete($quoteAddressId);
}
