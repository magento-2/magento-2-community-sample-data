<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Batch;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\ViewModel\DataProvider\BatchUrl;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * View model for dispatch list JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class BatchListing implements ArgumentInterface, ShippingApiInterface
{
    /**
     * @var ShippingApiAccess
     */
    private $apiAccess;

    /**
     * @var BatchUrl
     */
    private $batchUrl;

    /**
     * DispatchListing constructor.
     * @param ShippingApiAccess $apiAccess
     * @param BatchUrl $batchUrl
     */
    public function __construct(
        ShippingApiAccess $apiAccess,
        BatchUrl $batchUrl
    ) {
        $this->apiAccess = $apiAccess;
        $this->batchUrl = $batchUrl;
    }

    /**
     * @return ShippingApiAccessInterface
     */
    public function getShippingApiAccess(): ShippingApiAccessInterface
    {
        return $this->apiAccess;
    }

    /**
     * @return EntityUrlInterface|BatchUrl
     */
    public function getBatchUrl(): EntityUrlInterface
    {
        return $this->batchUrl;
    }
}
