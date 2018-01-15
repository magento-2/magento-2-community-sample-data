<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

use Temando\Shipping\Rest\Request\Type\OrderRequestTypeInterface;

/**
 * Temando API Order Operation Parameters
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderRequest implements OrderRequestInterface
{
    /**
     * @var OrderRequestTypeInterface
     */
    private $order;

    /**
     * @var string
     */
    private $orderId;

    /**
     * UpdateOrder constructor.
     *
     * @param OrderRequestTypeInterface $order
     * @param string $orderId
     */
    public function __construct(OrderRequestTypeInterface $order, $orderId = null)
    {
        $this->order = $order;
        $this->orderId = $orderId;
    }

    /**
     * @return string[]
     */
    public function getPathParams()
    {
        if (!$this->orderId) {
            return [];
        }

        return [
            $this->orderId,
        ];
    }

    /**
     * @return mixed[]
     */
    public function getRequestParams()
    {
        if ($this->orderId) {
            return [];
        }

        // Persist only the final Temando order transmission
        if ($this->order->isRealOrderRequest()) {
            return [
                'action' => 'orderQualification',
            ];
        }

        return [
            'action'  => 'orderQualification',
            'persist' => 'false',
        ];
    }

    /**
     * @return string
     */
    public function getRequestBody()
    {
        return json_encode($this->order, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
