<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Utility;

use Vertex\Data\DeliveryTerm as Term;
use Vertex\Services\Invoice\RequestInterface as InvoiceRequest;
use Vertex\Services\Quote\RequestInterface as QuoteRequest;

/**
 * Delivery Term Formatter for Vertex API Calls
 */
class DeliveryTerm
{
    /**
     * Add a Delivery Term to a Line Item if applicable
     *
     * @param QuoteRequest|InvoiceRequest $request
     * @return QuoteRequest|InvoiceRequest Same object supplied to $request
     */
    public function addIfApplicable($request)
    {
        if ($request->getSeller()
            && $request->getSeller()->getPhysicalOrigin()
            && $request->getSeller()->getPhysicalOrigin()->getCountry() === 'USA'
            && $request->getCustomer()
            && $request->getCustomer()->getDestination()
            && $request->getCustomer()->getDestination()->getCountry() === 'CAN'
        ) {
            return $request->setDeliveryTerm(Term::SUP);
        }

        return $request;
    }
}
