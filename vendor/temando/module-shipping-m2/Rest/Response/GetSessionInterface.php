<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Authentication Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface GetSessionInterface
{
    /**
     * @return \Temando\Shipping\Rest\Response\Type\SessionResponseType
     */
    public function getData();

    /**
     * @param \Temando\Shipping\Rest\Response\Type\SessionResponseType $data
     * @return void
     */
    public function setData($data);
}
