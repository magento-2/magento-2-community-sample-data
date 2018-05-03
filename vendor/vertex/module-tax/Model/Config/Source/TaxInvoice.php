<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Contains options for when to submit an Order to the Vertex Tax Log
 */
class TaxInvoice implements OptionSourceInterface
{
    /**
     * Available options for when to submit to the Vertex Tax log
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'When Invoice Created',
                'value' => 'invoice_created'
            ],

            [
                'label' => 'When Order Status Is Changed',
                'value' => 'order_status'
            ]
        ];
    }
}
