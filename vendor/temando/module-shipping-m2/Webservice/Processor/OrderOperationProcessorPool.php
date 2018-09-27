<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Api\Data\OrderInterface as SalesOrderInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Webservice\Processor\OrderOperation\RatesProcessorInterface;
use Temando\Shipping\Webservice\Processor\OrderOperation\SaveProcessorInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Order Response Processor Pool
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderOperationProcessorPool
{
    /**
     * @var RatesProcessorInterface[]
     */
    private $ratesProcessors;

    /**
     * @var SaveProcessorInterface[]
     */
    private $saveProcessors;

    /**
     * OrderOperationProcessorPool constructor.
     * @param RatesProcessorInterface[] $ratesProcessors
     * @param SaveProcessorInterface[] $saveProcessors
     */
    public function __construct(
        array $ratesProcessors = [],
        array $saveProcessors = []
    ) {
        $this->ratesProcessors = $ratesProcessors;
        $this->saveProcessors = $saveProcessors;
    }

    /**
     * @param SalesOrderInterface $salesOrder
     * @param OrderInterface $requestType
     * @param OrderResponseTypeInterface $responseType
     * @return void
     * @throws LocalizedException
     */
    public function processSaveResponse(
        SalesOrderInterface $salesOrder,
        OrderInterface $requestType,
        OrderResponseTypeInterface $responseType
    ) {
        foreach ($this->saveProcessors as $processor) {
            $processor->postProcess($salesOrder, $requestType, $responseType);
        }
    }

    /**
     * @param RateRequest $rateRequest
     * @param OrderInterface $requestType
     * @param OrderResponseTypeInterface $responseType
     * @return ShippingExperienceInterface[]
     * @throws LocalizedException
     */
    public function processRatesResponse(
        RateRequest $rateRequest,
        OrderInterface $requestType,
        OrderResponseTypeInterface $responseType
    ) {
        $rates = [];

        foreach ($this->ratesProcessors as $processor) {
            $processorRates = $processor->postProcess($rateRequest, $requestType, $responseType);
            $rates = array_merge($rates, $processorRates);
        }

        return $rates;
    }
}
