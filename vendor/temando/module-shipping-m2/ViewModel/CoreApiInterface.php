<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel;

use Temando\Shipping\ViewModel\DataProvider\CoreApiAccessInterface;

/**
 * M2 API Access Details Provider Interface
 *
 * All view models that provide access to the M2 Core API must implement this.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface CoreApiInterface
{
    /**
     * @return CoreApiAccessInterface
     */
    public function getCoreApiAccess(): CoreApiAccessInterface;
}
