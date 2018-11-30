<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Api;

use Magento\Store\Model\ScopeInterface;
use Vertex\Exception\ApiException;
use Vertex\Exception\ConfigurationException;
use Vertex\Exception\ValidationException;
use Vertex\Services\TaxAreaLookup\RequestInterface;
use Vertex\Services\TaxAreaLookup\ResponseInterface;

/**
 * Tax Area Lookup Service
 *
 * This message initiates a single Tax Area lookup requested by an external system. Use the Tax Area Request message to
 * send a query to retrieve a single tax jurisdiction lookup. For example, you could use this event to retrieve the Tax
 * Area IDs for a single postal address.
 *
 * Please see {@see RequestInterface} for more information on the fields available.
 *
 * @api
 */
interface TaxAreaLookupInterface
{
    /**
     * Look up the tax areas for an address
     *
     * @param RequestInterface $request
     * @param string|null $scopeCode Scope ID
     * @param string $scopeType
     * @return ResponseInterface
     * @throws ApiException
     * @throws ConfigurationException
     * @throws ValidationException
     */
    public function lookup(RequestInterface $request, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE);
}
