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
use Vertex\Services\Quote\RequestInterface;
use Vertex\Services\Quote\ResponseInterface;

/**
 * Quotation Request Service
 *
 * This message initiates a Quotation request. Use the Quotation message to estimate taxes on a proposed sale, rental,
 * or lease of goods or services by the Seller. Quotations may be called from a CRM, Mobile Sale, Order Entry, or
 * Internet Sale application. Quotation transactions are subject to change until an invoice is finalized. Consequently,
 * Quotation transactions are not written to the Tax Journal.
 *
 * Please see {@see RequestInterface} for more information on the fields available.
 *
 * @api
 */
interface QuoteInterface
{
    /**
     * Request a taxation quote from Vertex
     *
     * @param RequestInterface $request
     * @param string|null $scopeCode Scope ID
     * @param string $scopeType
     * @return ResponseInterface
     * @throws ApiException
     * @throws ConfigurationException
     * @throws ValidationException
     */
    public function request(RequestInterface $request, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE);
}
