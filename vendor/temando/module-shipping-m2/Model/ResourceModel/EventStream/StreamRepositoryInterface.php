<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\EventStream;

/**
 * Temando Shipment Stream Repository Interface.
 *
 *
 * @package  Temando\Shipping\Model
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface StreamRepositoryInterface
{
    /**
     * @param string $streamId
     * @return void
     */
    public function save($streamId);

    /**
     * @param string $streamId
     * @return void
     */
    public function delete($streamId);
}
