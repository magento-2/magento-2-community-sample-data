<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\PageAction;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Temando\Shipping\Model\PickupInterface;
use Temando\Shipping\Model\PickupProviderInterface;
use Temando\Shipping\ViewModel\DataProvider\PickupUrl;

/**
 * Action Button to Print a Packaging Slip for a Pickup Action
 *
 * @api
 * @package Temando\Shipping\Block
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupPrintButton extends Button
{
    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * @var PickupUrl
     */
    private $pickupUrl;

    /**
     * @param Context                 $context
     * @param PickupProviderInterface $pickupProvider
     * @param PickupUrl               $pickupUrl
     * @param mixed[]                 $data
     */
    public function __construct(
        Context $context,
        PickupProviderInterface $pickupProvider,
        PickupUrl $pickupUrl,
        array $data = []
    ) {
        $this->pickupProvider = $pickupProvider;
        $this->pickupUrl = $pickupUrl;

        parent::__construct($context, $data);
    }

    /**
     * Add button data
     *
     * @return string
     */
    protected function _toHtml()
    {
        $pickup = $this->pickupProvider->getPickup();
        $pickupId = $pickup->getPickupId();

        if ($pickup->getState() === PickupInterface::STATE_CANCELLED) {
            return '';
        }

        $printPickupUrl = $this->pickupUrl->getPrintActionUrl([
            'pickup_id' => $pickupId,
            'sales_order_id' => $this->pickupProvider->getOrder()->getEntityId(),
        ]);
        $this->setData('label', __('Print'));
        $this->setData('class', 'print');
        $this->setData('id', 'pickup-view-print-button');
        $this->setData('onclick', sprintf("setLocation('%s')", $printPickupUrl));

        return parent::_toHtml();
    }
}
