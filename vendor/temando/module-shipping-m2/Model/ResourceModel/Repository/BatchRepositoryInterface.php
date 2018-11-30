<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Repository;

/**
 * Temando Batch Repository Interface.
 *
 * Access batch.
 *
 * @package  Temando\Shipping\Model
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface BatchRepositoryInterface
{
    /**
     * @param string $batchId
     * @return \Temando\Shipping\Model\BatchInterface
     */
    public function getById($batchId);
}
