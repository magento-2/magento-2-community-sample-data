<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\Model\AbstractModel;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;
use Temando\Shipping\Model\ResourceModel\CollectionPoint\SearchRequest as SearchRequestResource;

/**
 * Temando Collection Point Search Request
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SearchRequest extends AbstractModel implements SearchRequestInterface
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
        return $this->getData(SearchRequestInterface::SHIPPING_ADDRESS_ID);
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        return $this->getData(SearchRequestInterface::COUNTRY_ID);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(SearchRequestInterface::POSTCODE);
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return (bool)$this->getData(SearchRequestInterface::PENDING);
    }
}
