<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper\JsonApi;

use Temando\Shipping\Rest\Response\DataObject\AbstractResource;

/**
 * Temando REST API JSON API Container Interface
 *
 * Register deserialized resources
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ResourceContainerInterface
{
    /**
     * Register a resource
     *
     * @param AbstractResource $resource
     * @return void
     */
    public function addResource(AbstractResource $resource);

    /**
     * Obtain a registered resource identified by type and id properties.
     *
     * @param string $type
     * @param string $id
     * @return AbstractResource
     */
    public function getResource($type, $id);

    /**
     * Obtain all registered resources.
     *
     * @return AbstractResource[]
     */
    public function getResources();
}
