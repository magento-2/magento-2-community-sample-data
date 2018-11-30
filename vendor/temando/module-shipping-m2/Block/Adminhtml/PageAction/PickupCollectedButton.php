<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Block\Adminhtml\PageAction;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Temando\Shipping\Model\Pickup\PickupManagementFactory;
use Temando\Shipping\Model\PickupProviderInterface;
use Temando\Shipping\ViewModel\DataProvider\PickupUrl;

/**
 * Action Button to Pickup Collected Action.
 *
 * @api
 * @package Temando\Shipping\Block
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 *
 */
class PickupCollectedButton extends Button
{
    /**
     * @var PickupProviderInterface
     */
    private $pickupProvider;

    /**
     * @var PickupManagementFactory
     */
    private $pickupManagementFactory;

    /**
     * @var PickupUrl
     */
    private $pickupUrl;

    /**
     * @param Context                 $context
     * @param PickupProviderInterface $pickupProvider
     * @param PickupManagementFactory $pickupManagementFactory
     * @param PickupUrl               $pickupUrl
     * @param mixed[]                 $data
     */
    public function __construct(
        Context $context,
        PickupProviderInterface $pickupProvider,
        PickupManagementFactory $pickupManagementFactory,
        PickupUrl $pickupUrl,
        array $data = []
    ) {
        $this->pickupProvider = $pickupProvider;
        $this->pickupManagementFactory = $pickupManagementFactory;
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
        $pickupManagement = $this->pickupManagementFactory->create([
            'pickups' => $this->pickupProvider->getPickups(),
        ]);

        if (!$pickupManagement->canCollect($pickupId)) {
            return '';
        }

        $pickupCollectedUrl = $this->pickupUrl->getCollectedActionUrl([
            'pickup_id' => $pickupId,
            'sales_order_id' => $this->pickupProvider->getOrder()->getEntityId(),
        ]);

        $this->setData('label', __('Mark as Picked Up'));
        $this->setData('class', 'primary');
        $this->setData('type', 'submit');
        $this->setData('onclick', sprintf("setLocation('%s')", $pickupCollectedUrl));

        return parent::_toHtml();
    }
}
