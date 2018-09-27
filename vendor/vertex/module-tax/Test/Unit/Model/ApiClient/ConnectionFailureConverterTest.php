<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\ApiClient;

use Vertex\Tax\Exception\ApiRequestException\ConnectionFailureException;
use Vertex\Tax\Model\ApiClient\ConnectionFailureConverter;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Tests the functionality of the ConnectionFailureConverter
 */
class ConnectionFailureConverterTest extends TestCase
{
    /** @var ConnectionFailureConverter */
    private $converter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->converter = $this->getObject(ConnectionFailureConverter::class);
    }

    /**
     * Tests that any failure to load the WSDL results in a ConnectionFailureException
     *
     * @return void
     */
    public function testFailureToLoadWsdlConversion()
    {
        $errorMessage = 'SOAP-ERROR: Parsing WSDL: Couldn\'t load from \'' .
            'https://mgsconnect.vertexsmb.com/vertex-ws/services/LookupTaxAreas60?wsdl\' : failed to load external ' .
            'entity "https://mgsconnect.vertexsmb.com/vertex-ws/services/LookupTaxAreas60?wsdl"';

        $fault = new \SoapFault('WSDL', $errorMessage);
        
        $result = $this->converter->convert($fault);
        $this->assertInstanceOf(ConnectionFailureException::class, $result);
    }

    /**
     * Tests that a non-WSDL fault returns null
     *
     * @return void
     */
    public function testRandomFaultIsApiRequestException()
    {
        $errorMessage = rand();

        $fault = new \SoapFault('Server', $errorMessage);

        $result = $this->converter->convert($fault);
        $this->assertNull($result);
    }
}
