<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Delivery;

use Temando\Shipping\Api\Data\Delivery\CollectionPointSearchRequestInterface;
use Temando\Shipping\Model\ResourceModel\Db\NoSequenceDb;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Collection Point Search Request Resource Model
 *
 * @package Temando\Shipping\Model
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointSearchRequest extends NoSequenceDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            SetupSchema::TABLE_COLLECTION_POINT_SEARCH,
            CollectionPointSearchRequestInterface::SHIPPING_ADDRESS_ID
        );
    }
}
