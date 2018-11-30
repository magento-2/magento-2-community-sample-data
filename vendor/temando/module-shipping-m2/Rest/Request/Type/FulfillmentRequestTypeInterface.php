<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request\Type;

/**
 * Temando API Fulfillment Request Type
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface FulfillmentRequestTypeInterface
{
    /**
     * Read ID. Empty if not yet created at Temando platform.
     *
     * @return string
     */
    public function getId();

    /**
     * Update ID after fulfillment was created at Temando platform.
     *
     * @return void
     * @param string $id
     */
    public function setId($id);
}
