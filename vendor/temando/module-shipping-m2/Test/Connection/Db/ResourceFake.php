<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Test\Connection\Db;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Database Resource Model Fake for Tests
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
final class ResourceFake extends AbstractDb
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param string $connectionName
     * @param string $idFieldName
     */
    public function __construct(Context $context, $connectionName = null, $idFieldName = 'id')
    {
        $this->_idFieldName = $idFieldName;

        parent::__construct($context, $connectionName);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
    }

    /**
     * Load an object
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        return $this;
    }

    /**
     * Save object object data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this;
    }

    /**
     * Delete the object
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function delete(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this;
    }
}
