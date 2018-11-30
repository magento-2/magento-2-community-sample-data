<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

use Temando\Shipping\Rest\Response\Fields\StreamEventAttributes;

/**
 * Temando API Stream Event Resource Object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class StreamEvent extends AbstractResource
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\StreamEventAttributes
     */
    private $attributes;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\StreamEventAttributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\StreamEventAttributes $attributes
     * @return void
     */
    public function setAttributes(StreamEventAttributes $attributes)
    {
        $this->attributes = $attributes;
    }
}
