<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Get Completions Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class GetCompletions implements GetCompletionsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\CompletionResponseType[]
     */
    private $data = [];

    /**
     * Obtain response entity
     *
     * @return \Temando\Shipping\Rest\Response\Type\CompletionResponseType[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response entity
     *
     * @param \Temando\Shipping\Rest\Response\Type\CompletionResponseType[] $completions
     * @return void
     */
    public function setData(array $completions)
    {
        $this->data = $completions;
    }
}
