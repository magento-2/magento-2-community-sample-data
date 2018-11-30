<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

use Temando\Shipping\Rest\Response\DataObject\Session;

/**
 * Temando API Authentication Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface GetSessionInterface
{
    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Session
     */
    public function getData();

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Session $data
     * @return void
     */
    public function setData(Session $data);
}
