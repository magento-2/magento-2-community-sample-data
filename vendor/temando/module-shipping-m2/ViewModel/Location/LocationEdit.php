<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Location;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\ViewModel\PageActionsInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\DataProvider\LocationUrl;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;

/**
 * View model for location new/edit JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class LocationEdit implements ArgumentInterface, PageActionsInterface, ShippingApiInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ShippingApiAccess
     */
    private $apiAccess;

    /**
     * @var LocationUrl
     */
    private $locationUrl;

    /**
     * LocationEdit constructor.
     * @param RequestInterface $request
     * @param ShippingApiAccess $apiAccess
     * @param LocationUrl $locationUrl
     */
    public function __construct(
        RequestInterface $request,
        ShippingApiAccess $apiAccess,
        LocationUrl $locationUrl
    ) {
        $this->request = $request;
        $this->apiAccess = $apiAccess;
        $this->locationUrl = $locationUrl;
    }

    /**
     * Obtain array of button data.
     *
     * @see \Temando\Shipping\Block\Adminhtml\ComponentContainer::_prepareLayout
     * @see \Magento\Backend\Block\Widget\Button\ButtonList::add
     *
     * @return mixed[][]
     */
    public function getMainActions(): array
    {
        $buttonId = 'back';
        $buttonData = [
            'label' => __('Back'),
            'onclick' => sprintf("window.location.href = '%s';", $this->locationUrl->getListActionUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];

        $mainActions = [
            $buttonId => $buttonData,
        ];

        return $mainActions;
    }

    /**
     * @return ShippingApiAccessInterface
     */
    public function getShippingApiAccess(): ShippingApiAccessInterface
    {
        return $this->apiAccess;
    }

    /**
     * @return EntityUrlInterface|LocationUrl
     */
    public function getLocationUrl(): EntityUrlInterface
    {
        return $this->locationUrl;
    }

    /**
     * Obtain the Temando location id that is passed from grid to edit component.
     * Think of it as a GUID rather than a location id in the local storage.
     *
     * @return string The Temando location id
     */
    public function getLocationId(): string
    {
        $locationId = $this->request->getParam(LocationInterface::LOCATION_ID);
        return preg_replace('/[^\w0-9-_]/', '', $locationId);
    }
}
