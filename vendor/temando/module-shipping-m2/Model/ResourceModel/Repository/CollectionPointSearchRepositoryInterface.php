<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;

/**
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CollectionPointSearchRepositoryInterface
{
    /**
     * @param int $quoteAddressId
     * @return SearchRequestInterface
     * @throws NoSuchEntityException
     */
    public function get($quoteAddressId);

    /**
     * @param SearchRequestInterface $searchRequest
     * @return SearchRequestInterface
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
