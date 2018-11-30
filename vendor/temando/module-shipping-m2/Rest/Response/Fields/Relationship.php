<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

/**
 * Temando API Relationship Object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Relationship
{
    /**
     * @var mixed[]
     */
    private $links = [];

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Relationship\ResourceIdentifier[]
     */
    private $data;

    /**
     * @var mixed[]
     */
    private $meta = [];

    /**
     * @return mixed[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param mixed[] $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Relationship\ResourceIdentifier[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Relationship\ResourceIdentifier[] $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed[]
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param mixed[] $meta
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }
}
