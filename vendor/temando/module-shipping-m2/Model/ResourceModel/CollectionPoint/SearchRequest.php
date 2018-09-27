<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\ResourceModel\CollectionPoint;

use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;
use Temando\Shipping\Model\ResourceModel\Db\NoSequenceDb;
use Temando\Shipping\Setup\SetupSchema;

/**
 * Temando Collection Point Search Request Resource Model
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SearchRequest extends NoSequenceDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SetupSchema::TABLE_COLLECTION_POINT_SEARCH, SearchRequestInterface::SHIPPING_ADDRESS_ID);
    }
}
