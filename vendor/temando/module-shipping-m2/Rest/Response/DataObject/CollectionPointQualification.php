<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

use \Temando\Shipping\Rest\Response\Fields\CollectionPointQualificationAttributes;

/**
 * Temando API Collection Point Qualification Resource Object
 *
 * Note: The CollectionPoint document requires a separate data object because
 * it does not follow JSON API spec. Once the endpoint returns a valid document,
 * switch to the generic order qualification object,
 * @see OrderQualification
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CollectionPointQualification extends AbstractResource
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\CollectionPointQualificationAttributes[]
     */
    private $attributes = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\CollectionPointQualificationAttributes[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\CollectionPointQualificationAttributes[] $attributes
     * @return void
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }
}
