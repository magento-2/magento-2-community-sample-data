<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel;

/**
 * Return Shipment Details Provider Interface
 *
 * All view models that allow shipping an RMA must implement this.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ReturnShipInterface
{
    /**
     * @return string
     */
    public function getSaveShipmentIdsEndpoint(): string;

    /**
     * @return string
     */
    public function getReturnData(): string;

    /**
     * @return string
     */
    public function getExtOrderId(): string;

    /**
     * @return string
     */
    public function getRmaShipmentPageUrl(): string;

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
