<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\DataObject;
use Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface;

/**
 * Temando Collection Point Search Request
 *
 * @deprecated since 1.4.0
 * @see \Temando\Shipping\Model\Delivery\CollectionPointSearchRequest
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class SearchRequest extends DataObject implements SearchRequestInterface
{
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
