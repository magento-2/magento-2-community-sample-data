<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Fulfillment Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface GetFulfillmentsInterface
{
    /**
     * Obtain response entities
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\Fulfillment[]
     */
    public function getData();

    /**
     * Set response entities
     *
     * @param \Temando\Shipping\Rest\Response\DataObject\Fulfillment[] $fulfillments
     * @return void
     */
    public function setData(array $fulfillments);
}
