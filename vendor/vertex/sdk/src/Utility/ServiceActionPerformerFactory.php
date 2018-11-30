<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Utility;

/**
 * Factory for ServiceActionPerformer
 */
class ServiceActionPerformerFactory
{
    /**
     * Create an instance of {@see ServiceActionPerformer}
     *
     * The following parameters are required for instantiation:
     * - `string $url`: WSDL URI
     * - `string $method`: SOAP method to be called
     * - `object $requestMapper`: (must be an object with a "map" method, should return a \stdClass)
     * - `object $responseMapper`: (must be an object with a "build" method)
     * - `AuthenticatorInterface $authenticator`: {@see \Vertex\Mapper\AuthenticatorInterface}
     *
     * @param mixed[] $parameters Indexed by parameter name
     * @return ServiceActionPerformer
     */
    public function create(array $parameters)
    {
        $requiredParameters = ['url', 'method', 'requestMapper', 'responseMapper', 'authenticator'];
        foreach ($requiredParameters as $parameterName) {
            if (!isset($parameters[$parameterName]) || empty($parameters[$parameterName])) {
                throw new \InvalidArgumentException('Missing required parameter: ' . $parameterName);
            }
        }

        return new ServiceActionPerformer(
            $parameters['url'],
            $parameters['method'],
            $parameters['requestMapper'],
            $parameters['responseMapper'],
            $parameters['authenticator'],
            isset($parameters['soapClientFactory']) ? $parameters['soapClientFactory'] : null,
            isset($parameters['faultConverter']) ? $parameters['faultConverter'] : null
        );
    }
}
