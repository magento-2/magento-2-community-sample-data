<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

/**
 * Temando Carrier Repository Interface.
 *
 * Access a list of carriers as connected to the merchant's account.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CarrierRepositoryInterface
{
    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return \Temando\Shipping\Model\CarrierInterface[]
     */
    public function getList($offset = null, $limit = null);

    /**
     * @param string $carrierConfigurationId
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete($carrierConfigurationId);
}
