<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\DataObject;

/**
 * Temando API Resource Object
 *
 * Child classes may define further resource object members with type annotations:
 * - attributes
 * - links
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
abstract class AbstractResource
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $id;

    /**
     * One-to-one relationships
     *
     * @var \Temando\Shipping\Rest\Response\Fields\Relationship[]
     */
    private $relationships = [];

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Relationship[]
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Relationship[] $relationships
     */
    public function setRelationships(array $relationships)
    {
        $this->relationships = $relationships;
    }
}
