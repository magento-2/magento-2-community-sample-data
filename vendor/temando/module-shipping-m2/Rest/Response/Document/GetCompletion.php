<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

use Temando\Shipping\Rest\Response\DataObject\Completion;

/**
 * Temando API Get Completion Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetCompletion implements GetCompletionInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Completion
     */
    private $data;

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Completion
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Completion $completion
     * @return void
     */
    public function setData(Completion $completion)
    {
        $this->data = $completion;
    }
}
