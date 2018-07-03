<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Service contract for calls against the Vertex API
 *
 * Recommended usage of this class is for modifying the request or response to Vertex
 *
 * @api
 */
interface ClientInterface
{
    /**
     * Perform a SOAP call against Vertex
     *
     * @param array $request A properly formatted SOAP request object.  Please consult Vertex API documentation for
     *   more information
     * @param string $type One of tax_area_lookup, invoice, or quote
     * @param OrderInterface|null $order The order being invoiced or quoted; null if not applicable
     *
     * @return array|boolean A SOAP formatted response or false on failure
     */
    public function sendApiRequest(array $request, $type, OrderInterface $order = null);
}
