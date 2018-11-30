<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Pickup;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\ViewModel\DataProvider\PickupUrl as UrlProvider;

/**
 * View model for Pickup Action URLs.
 *
 * Wrapper around the pickup URL provider, usable as block argument.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Sebastian Ertner<sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupUrl implements ArgumentInterface
{
    /**
     * @var UrlProvider
     */
    private $urlProvider;

    /**
     * PickupUrl constructor.
     * @param UrlProvider $urlProvider
     */
    public function __construct(UrlProvider $urlProvider)
    {
        $this->urlProvider = $urlProvider;
    }

    /**
     * Creating pickup fulfillments via UI is not supported.
     *
     * @return string
     */
    public function getNewActionUrl(): string
    {
        return $this->urlProvider->getNewActionUrl();
    }

    /**
     * Link to the pickup fulfillment grid listing.
     *
     * @return string
     */
    public function getListActionUrl(): string
    {
        return $this->urlProvider->getListActionUrl();
    }

    /**
     * Link to the pickup detail view. Applies to the following pickup states:
     * - ready for pickup
     * - picked up
     * - cancelled
     *
     * @param mixed[] $data Item data for the implementer to pick entity identifier.
     * @return string
     */
    public function getViewActionUrl(array $data): string
    {
        return $this->urlProvider->getViewActionUrl($data);
    }

    /**
     * Link to the "Prepare for Pickup" page with editable quantities.
     *
     * @param mixed[] $data Item data for the implementer to pick entity identifier.
     * @return string
     */
    public function getEditActionUrl(array $data): string
    {
        return $this->urlProvider->getEditActionUrl($data);
    }

    /**
     * Link to the pickup cancel POST action.
     *
     * @param mixed[] $data Item data for the implementer to pick entity identifier.
     * @return string
     */
    public function getDeleteActionUrl(array $data): string
    {
        return $this->urlProvider->getDeleteActionUrl($data);
    }

    /**
     * Link to the "mark as ready for pickup" POST action
     *
     * @param mixed[] $data Item data to pick entity identifiers.
     * @return string
     */
    public function getReadyActionUrl(array $data): string
    {
        return $this->urlProvider->getReadyActionUrl($data);
    }

    /**
     * Link to the "mark as picked up" POST action
     *
     * @param mixed[] $data Item data for the implementer to pick entity identifier.
     * @return string
     */
    public function getCollectedActionUrl(array $data): string
    {
        return $this->urlProvider->getCollectedActionUrl($data);
    }
}
