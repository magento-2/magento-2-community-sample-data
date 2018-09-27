<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Vertex\Tax\Model\TaxRegistry\StorageInterface;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;

/**
 * Provide proprietary storage for Vertex tax information.
 */
class TaxRegistry
{
    const KEY_TAXES = 'vertex_tax_response';
    const KEY_ERROR_GENERIC = 'vertex_error_generic';

    /** @var StorageInterface */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(
        StorageInterface $storage
    ) {
        $this->storage = $storage;
    }

    /**
     * Determine whether calculated tax data is available.
     *
     * @return bool
     */
    public function hasTaxes()
    {
        return $this->lookup(self::KEY_TAXES) !== null;
    }

    /**
     * Attempt to retrieve an item from the registry by key.
     *
     * @param string $key
     * @return mixed
     */
    public function lookup($key)
    {
        return $this->storage->get($key);
    }

    /**
     * Retrieve calculated tax data from the registry.
     *
     * @return TaxQuoteResponse|null
     */
    public function lookupTaxes()
    {
        return $this->lookup(self::KEY_TAXES);
    }

    /**
     * Store information in the registry.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function register($key, $value)
    {
        return $this->storage->set($key, $value);
    }

    /**
     * Register an error message.
     *
     * @param string $message
     * @param string $type
     * @return bool
     */
    public function registerError($message, $type = self::KEY_ERROR_GENERIC)
    {
        $this->unregister($type);

        return $this->register($type, $message);
    }

    /**
     * Store calculated tax data in its registry slot.
     *
     * @param TaxQuoteResponse $taxInfo
     * @return bool
     */
    public function registerTaxes(TaxQuoteResponse $taxInfo)
    {
        $this->unregister(self::KEY_TAXES);
        return $this->register(self::KEY_TAXES, $taxInfo->getQuoteTaxedItems());
    }

    /**
     * Remove information from the registry.
     *
     * @param string $key
     * @return bool
     */
    public function unregister($key)
    {
        return $this->storage->unsetData($key);
    }

    /**
     * Remove calculated tax data from its registry slot.
     *
     * @return bool
     */
    public function unregisterTaxes()
    {
        return $this->storage->unsetData(self::KEY_TAXES);
    }
}
