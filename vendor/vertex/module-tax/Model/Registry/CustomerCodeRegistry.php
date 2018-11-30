<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Registry;

/**
 * Maintains a state of customer ID and customer codes to prevent extraneous calls to the database
 */
class CustomerCodeRegistry
{
    /** @var string[] Indexed by Customer ID */
    private $registry = [];

    /**
     * Retrieve a customer code stored in the registry
     *
     * @param string $customerId
     * @return bool|string
     */
    public function get($customerId)
    {
        if (array_key_exists($customerId, $this->registry)) {
            return $this->registry[$customerId];
        }
        return false;
    }

    /**
     * Store a customer code in the registry
     *
     * @param string $customerId
     * @param string $customerCode
     * @return CustomerCodeRegistry
     */
    public function set($customerId, $customerCode)
    {
        $this->registry[$customerId] = $customerCode;
        return $this;
    }

    /**
     * Delete a customer code from the registry
     *
     * @param string $customerId
     * @return CustomerCodeRegistry
     */
    public function delete($customerId)
    {
        unset($this->registry[$customerId]);
        return $this;
    }
}
