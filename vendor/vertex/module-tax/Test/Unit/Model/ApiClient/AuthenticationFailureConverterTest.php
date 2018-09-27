<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\ApiClient;

use Vertex\Tax\Exception\ApiRequestException\AuthenticationException;
use Vertex\Tax\Model\ApiClient\AuthenticationFailureConverter;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Tests the functionality of the AuthenticationFailureConverter
 */
class AuthenticationFailureConverterTest extends TestCase
{
    /** @var AuthenticationFailureConverter */
    private $converter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->converter = $this->getObject(AuthenticationFailureConverter::class);
    }

    /**
     * Tests that any failure to load the WSDL results in a ConnectionFailureException
     *
     * @return void
     */
    public function testAuthenticationFailureReturnsException()
    {
        $trustedIdCompanyCode = 'The Trusted ID could not be resolved, please check your connector configuration. '.
            'Note that Trusted IDs and Company Codes are case sensitive.';

        $fault = new \SoapFault('Server', $trustedIdCompanyCode);

        $result = $this->converter->convert($fault);
        $this->assertInstanceOf(AuthenticationException::class, $result);
    }

    /**
     * Tests that a non-Authentication fault returns null
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
