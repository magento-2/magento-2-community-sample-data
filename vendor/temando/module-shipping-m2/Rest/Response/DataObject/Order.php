<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

use Temando\Shipping\Rest\Response\Fields\OrderAttributes;

/**
 * Temando API Order Resource Object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Order extends AbstractResource
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\OrderAttributes
     */
    private $attributes;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\OrderAttributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\OrderAttributes $attributes
     * @return void
     */
    public function setAttributes(OrderAttributes $attributes)
    {
        $this->attributes = $attributes;
    }
}
