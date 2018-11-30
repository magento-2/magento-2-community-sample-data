<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\ApiExceptions;

use Vertex\Services\Quote\Request;
use Vertex\Tax\Api\QuoteInterface;
use Vertex\Tax\Test\Integration\TestCase;

/**
 * Ensure that making an API Request with an incomplete company address results in a generic exception
 */
class IncompleteCompanyAddressTest extends TestCase
{
    /** @var QuoteInterface */
    private $quote;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $om = $this->getObjectManager();

        // We're mocking the SOAP Response, as we don't want to hit the real API during testing
        $soapFactory = $this->getSoapFactory();
        $soap = $this->getMockBuilder(\SoapClient::class)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods()
            ->setMethods(['CalculateTax60'])
            ->getMock();
        $soapFactory->setSoapClient($soap);

        $fault = new \SoapFault(
            'soapenv:Client',
            'The LocationRole being added is invalid. This might be due to an invalid location or an invalid'
            . ' address field. Make sure that the locationRole is valid, and try again.'
        );

        $soap->method('CalculateTax60')
            ->willThrowException($fault);

        $this->quote = $om->get(QuoteInterface::class);
    }

    /**
     * Ensure that making an API Request with an incomplete company address results in a generic exception
     *
     * @expectedException \Vertex\Exception\ApiException
     */
    public function testSomething()
    {
        $this->quote->request(new Request());
    }
}
