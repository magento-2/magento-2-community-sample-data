<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel;

/**
 * Order and Shipment Details Provider Interface
 *
 * All view models that provide access to shipment action details must implement this.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderShipInterface
{
    /**
     * @return string
     */
    public function getShipEndpoint(): string;

    /**
     * @return string
     */
    public function getOrderData(): string;

    /**
     * @return string
     */
    public function getSelectedExperience(): string;

    /**
     * @return string
     */
    public function getExtOrderId(): string;

    /**
     * @return string
     */
    public function getShipmentViewPageUrl(): string;

    /**
     * @return string
     */
    public function getDefaultCurrency(): string;

    /**
     * @return string
     */
    public function getDefaultDimensionsUnit(): string;

    /**
     * @return string
     */
    public function getDefaultWeightUnit(): string;
}
