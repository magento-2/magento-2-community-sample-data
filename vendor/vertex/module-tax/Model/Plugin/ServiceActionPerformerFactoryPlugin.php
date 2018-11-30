<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Framework\ObjectManagerInterface;
use Vertex\Utility\ServiceActionPerformer;
use Vertex\Utility\ServiceActionPerformerFactory;
use Vertex\Utility\SoapClientFactory;

/**
 * Replaces a Vertex SDK Factory with Magento 2 dependency injection
 */
class ServiceActionPerformerFactoryPlugin
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Change Vertex SDK's Factory to utilize ObjectManager
     *
     * @todo Convert to afterCreate once we only support Magento 2.2+
     *
     * @param ServiceActionPerformerFactory $factory
     * @param callable $proceed
     * @param array $parameters
     * @return ServiceActionPerformer
     */
    public function aroundCreate(ServiceActionPerformerFactory $factory, callable $proceed, array $parameters)
    {
        // Call the original to trigger its checks & exceptions
        $performer = $proceed($parameters);
        unset($performer);

        if (!isset($parameters['soapClientFactory'])) {
            // This is necessary to ensure that the plugins for the SoapClientFactory are utilized
            $parameters['soapClientFactory'] = $this->objectManager->get(SoapClientFactory::class);
        }

        return $this->objectManager->create(ServiceActionPerformer::class, $parameters);
    }
}
