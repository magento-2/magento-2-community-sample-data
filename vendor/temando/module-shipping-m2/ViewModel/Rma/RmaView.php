<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Rma;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\ViewModel\RmaAccessInterface;

/**
 * View model for RMA related blocks.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner<sebastian.ertner@netresearch.de>
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
     * RmaView constructor.
     * @param RmaAccess $rmaAccess
     */
    public function __construct(RmaAccess $rmaAccess)
    {
        $this->rmaAccess = $rmaAccess;
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
     * @return string
     */
    public function getReturnShipmentId()
    {
        $returnShipment = $this->rmaAccess->getCurrentRmaShipment();
        if (!$returnShipment) {
            return '';
        }

        return $returnShipment->getShipmentId();
    }

    /**
     * @deprecated since 1.2.0 | no longer available
     * @return ShipmentInterface
     */
    public function getRmaShipment(): ShipmentInterface
    {
        return $this->rmaAccess->getCurrentRmaShipment();
    }
}
