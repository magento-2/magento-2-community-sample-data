<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\ViewModel\RmaAccessInterface;

/**
 * View model for RMA related blocks.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class RmaView implements ArgumentInterface, RmaAccessInterface
{
    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var OrderAddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * RmaView constructor.
     * @param RmaAccess $rmaAccess
     * @param OrderAddressInterfaceFactory $addressFactory
     * @param AddressRenderer $addressRenderer
     */
    public function __construct(
        RmaAccess $rmaAccess,
        OrderAddressInterfaceFactory $addressFactory,
        AddressRenderer $addressRenderer
    ) {
        $this->rmaAccess = $rmaAccess;
        $this->addressFactory = $addressFactory;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        /** @var \Magento\Rma\Model\Rma $rma */
        $rma = $this->getRma();
        return $rma->getOrder();
    }

    /**
     * @return RmaInterface
     */
    public function getRma(): RmaInterface
    {
        return $this->rmaAccess->getCurrentRma();
    }

    /**
     * @return ShipmentInterface
     */
    public function getRmaShipment(): ShipmentInterface
    {
        return $this->rmaAccess->getCurrentRmaShipment();
    }
}
