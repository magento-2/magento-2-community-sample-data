<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Dispatch Provider
 *
 * Registry for re-use of the same dispatch entity during one request cycle.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchProvider implements DispatchProviderInterface
{
    /**
     * @var DispatchInterface
     */
    private $dispatch;

    /**
     * @return DispatchInterface
     */
    public function getDispatch()
    {
        return $this->dispatch;
    }

    /**
     * @param DispatchInterface $dispatch
     * @return void
     */
    public function setDispatch(DispatchInterface $dispatch)
    {
        $this->dispatch = $dispatch;
    }
}
