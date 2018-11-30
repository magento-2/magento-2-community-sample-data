<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\Mock;

use Vertex\Utility\SoapClientFactory;

/**
 * Provides a way for tests to mock the SOAP Response and handle the SOAP Request without mocking ApiClient
 */
class SoapFactoryMock extends SoapClientFactory
{
    /** @var \SoapClient */
    private $client;

    /**
     * Set the SOAP Client to be returned from a creation
     *
     * @param \SoapClient $client
     */
    public function setSoapClient(\SoapClient $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function create($wsdl, array $options = [])
    {
        return $this->client;
    }
}
