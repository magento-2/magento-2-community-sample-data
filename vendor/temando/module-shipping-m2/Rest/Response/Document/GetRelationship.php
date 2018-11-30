<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Get Completion Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class GetRelationship
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Relationship
     */
    private $data;

    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Relationship
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Relationship $relationship
     * @return void
     */
    public function setData(Relationship $relationship)
    {
        $this->data = $relationship;
    }
}
