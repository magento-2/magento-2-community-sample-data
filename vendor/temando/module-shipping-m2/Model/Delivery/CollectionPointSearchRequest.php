<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\Delivery\CollectionPointSearchRequestInterface;
use Temando\Shipping\Model\ResourceModel\Delivery\CollectionPointSearchRequest as SearchRequestResource;

/**
 * Temando Collection Point Search Request
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointSearchRequest extends AbstractModel implements CollectionPointSearchRequestInterface
{
    /**
     * Init resource model.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(SearchRequestResource::class);
    }

    /**
     * @return int
     */
    public function getShippingAddressId()
    {
        return $this->getData(CollectionPointSearchRequestInterface::SHIPPING_ADDRESS_ID);
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        return $this->getData(CollectionPointSearchRequestInterface::COUNTRY_ID);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(CollectionPointSearchRequestInterface::POSTCODE);
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return (bool)$this->getData(CollectionPointSearchRequestInterface::PENDING);
    }
}
