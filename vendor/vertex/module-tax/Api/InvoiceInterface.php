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
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Services\Invoice\ResponseInterface;

/**
 * Invoice Record Service
 *
 * This request initiates tax calculation on an invoice. Use this message to calculate tax at the time of shipping,
 * billing, or invoicing from the seller's perspective. Because tax liability is typically incurred at the point of
 * invoicing Invoice transactions are written to the Tax Journal.
 *
 * Please see {@see RequestInterface} for more information on the fields available.
 *
 * @api
 */
interface InvoiceInterface
{
    /**
     * Record an invoice to the Vertex Tax Log
     *
     * @param RequestInterface $request
     * @param string|null $scopeCode Scope ID
     * @param string $scopeType Scope Type
     * @return ResponseInterface
     * @throws ApiException
     * @throws ConfigurationException
     * @throws ValidationException
     */
    public function record(RequestInterface $request, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE);
}
