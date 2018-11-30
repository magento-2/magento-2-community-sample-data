<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Qualify Order Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface QualifyOrderInterface extends CompoundDocumentInterface
{
    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Order
     */
    public function getData();

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Order $order
     * @return void
     */
    public function setData(\Temando\Shipping\Rest\Response\DataObject\Order $order);
}
