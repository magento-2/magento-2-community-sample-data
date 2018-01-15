<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Completion Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetCompletion implements GetCompletionInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\CompletionResponseType
     */
    private $data;

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\Type\CompletionResponseType
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entity
     *
     * @param \Temando\Shipping\Rest\Response\Type\CompletionResponseType $completion
     * @return void
     */
    public function setData($completion)
    {
        $this->data = $completion;
    }
}
