<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\DataObject;

/**
 * Temando Batch Entity
 *
 * This model contains the data used in the shipping module, not necessarily all
 * data available in its webservice representation.
 *
 * @package  Temando\Shipping\Model
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Batch extends DataObject implements BatchInterface
{
    /**
     * @return string
     */
    public function getBatchId()
    {
        return $this->getData(self::BATCH_ID);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string
     */
    public function getCreatedAtDate()
    {
        return $this->getData(self::CREATED_AT_DATE);
    }

    /**
     * @return string
     */
    public function getUpdatedAtDate()
    {
        return $this->getData(self::UPDATED_AT_DATE);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\ShipmentSummaryInterface[]
     */
    public function getIncludedShipments()
    {
        return $this->getData(self::INCLUDED_SHIPMENTS);
    }

    /**
     * @return \Temando\Shipping\Model\Shipment\ShipmentSummaryInterface[]
     */
    public function getFailedShipments()
    {
        return $this->getData(self::FAILED_SHIPMENTS);
    }

    /**
     * @return DocumentationInterface[]
     */
    public function getDocumentation()
    {
        return $this->getData(self::DOCUMENTATION);
    }
}
