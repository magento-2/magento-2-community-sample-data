<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Processor\OrderOperation;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface as SalesOrderInterface;
use Temando\Shipping\Model\OrderInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;

/**
 * Temando Order Save Response Processor Interface
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface SaveProcessorInterface
{
    /**
     * @param SalesOrderInterface $salesOrder
     * @param OrderInterface $requestType
     * @param OrderResponseTypeInterface $responseType
     * @return void
     * @throws LocalizedException
     */
    public function postProcess(
        SalesOrderInterface $salesOrder,
        OrderInterface $requestType,
        OrderResponseTypeInterface $responseType
    );
}
