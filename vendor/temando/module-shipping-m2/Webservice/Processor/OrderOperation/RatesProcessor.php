<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Rates Processor.
 *
 * @package Temando\Shipping\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class RatesProcessor implements RatesProcessorInterface
{
    /**
     * Extract shipping experiences from response.
     *
     * @param RateRequest $rateRequest
     * @param OrderInterface $requestType
     * @param OrderResponseTypeInterface $responseType
     * @return ShippingExperienceInterface[]
     */
    public function postProcess(
        RateRequest $rateRequest,
        OrderInterface $requestType,
        OrderResponseTypeInterface $responseType
    ) {
        if ($requestType->getCollectionPoint() || $requestType->getPickupLocation()) {
            // ignore default recipient address experiences
            return [];
        }

        // experiences for default addresses
        $experiences = $responseType->getShippingExperiences();

        return $experiences;
    }
}
