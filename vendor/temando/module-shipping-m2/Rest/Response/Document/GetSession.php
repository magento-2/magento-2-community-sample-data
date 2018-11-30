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
class GetSession implements GetSessionInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Session
     */
    private $data;

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Session
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Session $data
     * @return void
     */
    public function setData(Session $data)
    {
        $this->data = $data;
    }
}
