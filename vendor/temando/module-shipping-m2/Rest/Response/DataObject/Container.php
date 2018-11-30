<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

/**
 * Temando API Container Resource Object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Container extends AbstractResource
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\ContainerAttributes
     */
    private $attributes;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\ContainerAttributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\ContainerAttributes $attributes
     *
     * @return void
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
}
