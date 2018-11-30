<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Vertex\Tax\Model\TaxRegistry\StorageInterface;
use Vertex\Tax\Model\TaxQuote\TaxQuoteResponse;

/**
 * Provide proprietary storage for Vertex tax information.
 */
class TaxRegistry
{
    const KEY_TAXES = 'vertex_tax_response';
    const KEY_ERROR_GENERIC = 'vertex_error_generic';
    const LIFETIME_DEFAULT = 300;

    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var StorageInterface */
    private $storage;

    /**
     * In-object storage of error messages to persist for the life of the request.
     *
     * @var array
     */
    private $errorInfo = [];

    /**
     * In-object storage of calculated taxes to persist for the life of the request.
     *
     * @var DataObject[]
     */
    private $calculatedTaxInfo;

    /**
     * @param DataObjectFactory $dataObjectFactory,
     * @param StorageInterface $storage
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        StorageInterface $storage
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->storage = $storage;
    }

    /**
     * Determine whether error information is available.
     *
     * @param string $type
     * @return bool
     */
    public function hasError($type = self::KEY_ERROR_GENERIC)
    {
        return !empty($this->errorInfo[$type]);
    }

    /**
     * Determine whether calculated tax data is available.
     *
     * @return bool
     */
    public function hasTaxes()
    {
        return !empty($this->calculatedTaxInfo);
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
     * @return DataObject[]|null
     */
    public function lookupTaxes()
    {
        return $this->calculatedTaxInfo ?: null;
    }

    /**
     * Retrieve stored error message.
     *
     * @param string $type
     * @return string|null
     */
    public function lookupError($type = self::KEY_ERROR_GENERIC)
    {
        return !empty($this->errorInfo[$type]) ? $this->errorInfo[$type] : null;
    }

    /**
     * Store information in the registry.
     *
     * @param string $key
     * @param mixed $value
     * @param int $lifetime
     * @return bool
     */
    public function register($key, $value, $lifetime = self::LIFETIME_DEFAULT)
    {
        return $this->storage->set($key, $value, $lifetime);
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
        $this->errorInfo[$type] = $message;

        return true;
    }

    /**
     * Store calculated tax data in its registry slot.
     *
     * @param TaxQuoteResponse $taxInfo
     * @return bool
     */
    public function registerTaxes(TaxQuoteResponse $taxInfo)
    {
        $this->calculatedTaxInfo = $taxInfo->getQuoteTaxedItems();

        return !empty($this->calculatedTaxInfo);
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
    public function unregisterError($type = null)
    {
        if ($type === null) {
            $this->errorInfo = [];
        } elseif (isset($this->errorInfo[$type])) {
            $this->errorInfo[$type] = null;
        } else {
            return false;
        }

        return true;
    }

    /**
     * Remove calculated tax data from its registry slot.
     *
     * @return bool
     */
    public function unregisterTaxes()
    {
        $this->calculatedTaxInfo = null;

        return empty($this->calculatedTaxInfo);
    }
}
