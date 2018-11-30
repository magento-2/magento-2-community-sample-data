<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

use Temando\Shipping\Rest\Request\Type\FulfillmentRequestTypeInterface;

/**
 * Temando API Create/Update Fulfillment Operation Parameters
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class FulfillmentRequest implements FulfillmentRequestInterface
{
    /**
     * @var FulfillmentRequestTypeInterface
     */
    private $fulfillment;

    /**
     * OrderRequest constructor.
     *
     * @param FulfillmentRequestTypeInterface $fulfillment
     */
    public function __construct(FulfillmentRequestTypeInterface $fulfillment)
    {
        $this->fulfillment = $fulfillment;
    }

    /**
     * @return string[]
     */
    public function getPathParams()
    {
        if (!$this->fulfillment->getId()) {
            return [];
        }

        return [
            $this->fulfillment->getId(),
        ];
    }

    /**
     * @return string
     */
    public function getRequestBody()
    {
        return json_encode($this->fulfillment, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
