<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Block\Plugin;

use Magento\Sales\Block\Adminhtml\Order\Create\Form;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Perform quote totals calculation.
 *
 * Initiates an early call to totals collection before child blocks are rendered. This is required
 * because totals collection occurs at render time on a child block which is placed after the
 * messages block. Since the messages block renders first, we must initialize totals collection
 * beforehand as to set and expose any Vertex errors.
 */
class OrderCreateFormPlugin
{
    /**
     * Prepare order data JSON. Trigger quote totals collection.
     *
     * @param  Form $subject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeGetOrderDataJson(Form $subject)
    {
        $block = $subject->getLayout()->getBlock('totals');

        if ($block instanceof BlockInterface) {
            $block->getTotals();
        }
    }
}
