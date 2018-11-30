<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Adapter;

use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\AuthRequestInterface;
use Temando\Shipping\Rest\Response\DataObject\Session;

/**
 * The Temando Authentication API interface defines the supported subset of
 * operations as available at the Temando API.
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface AuthenticationApiInterface
{
    /**
     * @param AuthRequestInterface $request
     * @return Session
     * @throws AdapterException
     */
    public function startSession(AuthRequestInterface $request);

    /**
     * @return bool
     */
    public function endSession();
}
