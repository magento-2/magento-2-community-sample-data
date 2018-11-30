<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Stream Event Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface GetStreamEventsInterface
{
    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\StreamEvent[]
     */
    public function getData();

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\StreamEvent[] $events
     * @return void
     */
    public function setData(array $events);
}
