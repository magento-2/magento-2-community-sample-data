<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Vertex\Tax\Model\Api\Utility\SoapClientRegistry;
use Vertex\Utility\SoapClientFactory;

/**
 * Plugin to SoapClientFactory
 */
class SoapClientFactoryPlugin
{
    /** @var SoapClientRegistry */
    private $clientRegistry;

    /**
     * @param SoapClientRegistry $clientRegistry
     */
    public function __construct(SoapClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    /**
     * After a {@see \SoapClient} is created, set it as the latest in the client registry
     *
     * @param SoapClientFactory $factory
     * @param \SoapClient $client
     * @return \SoapClient
     */
    public function afterCreate(SoapClientFactory $factory, \SoapClient $client)
    {
        $this->clientRegistry->setLastClient($client);
        return $client;
    }

    /**
     * Add a connection timeout of 12 to the default options used by {@see SoapClientFactory}
     *
     * @param SoapClientFactory $factory
     * @param array $options
     * @return array
     */
    public function afterGetDefaultOptions(SoapClientFactory $factory, array $options)
    {
        return array_merge(
            $options,
            [
                'stream_context' => [
                    'connection_timeout' => 12
                ]
            ]
        );
    }
}
