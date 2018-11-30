<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Dispatch;

use Magento\Framework\DataObject;

/**
 * Temando Dispatch Shipment
 *
 * @deprecated since 1.3.0
 * @see \Temando\Shipping\Model\Shipment\ShipmentError
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Error extends DataObject implements ErrorInterface
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(ErrorInterface::TITLE);
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->getData(ErrorInterface::DETAIL);
    }
}
