<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

/**
 * Temando Dispatch Repository Interface.
 *
 * Access dispatch/completion documents.
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface DispatchRepositoryInterface
{
    /**
     * @param string $dispatchId
     * @return \Temando\Shipping\Model\DispatchInterface
     */
    public function getById($dispatchId);
}
