<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Magento\Quote\Model\Quote\Address as QuoteAddress;

/**
 * Delivery Term Formatter for Vertex API Calls
 */
class DeliveryTerm
{
    /**
     * Add a Delivery Term to a Line Item if applicable
     *
     * @param array $data
     * @param Address $taxAddress
     * @return array
     */
    public function addDeliveryTerm($data, QuoteAddress $taxAddress)
    {
        if ($taxAddress->getCountryId() === 'CA') {
            $data['deliveryTerm'] = 'SUP';
        }

        return $data;
    }
}
