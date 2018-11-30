<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice;

use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Webservice\Exception\HttpRequestException;
use Temando\Shipping\Webservice\Exception\HttpResponseException;

/**
 * HttpClientTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class HttpClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @test
     */
    public function setHeaders()
    {
        $zendClient = new \Zend\Http\Client();
        $headers = [
            'foo' => 'bar',
            'fox' => 'baz',
        ];

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $httpClient->setHeaders($headers);

        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $zendClient->getHeader($key));
        }
    }

    /**
     * @test
     */
    public function setUri()
    {
        $zendClient = new \Zend\Http\Client();
        $uri = 'https://example.org/';

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $httpClient->setUri($uri);

        $this->assertEquals($uri, $zendClient->getUri());
    }

    /**
     * @test
     */
    public function setOptions()
    {
        $zendClient = new \Zend\Http\Client();
        $options = ['trace' => 1, 'maxredirects' => 23, 'timeout' => 42, 'useragent' => 'Foo'];

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $httpClient->setOptions($options);

        foreach ($options as $key => $value) {
            $adapterConfig = $zendClient->getAdapter()->getConfig();
            $this->assertEquals($value, $adapterConfig[$key]);
        }
    }

    /**
     * @test
     */
    public function setBody()
    {
        $zendClient = new \Zend\Http\Client();
        $body = '{"error": "foo"}';

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $httpClient->setRawBody($body);

        $this->assertEquals($body, $zendClient->getRequest()->getContent());
    }

    /**
     * @test
     */
    public function setQueryParams()
    {
        $zendClient = new \Zend\Http\Client();
        $query = [
            'limit' => 23,
            'offset' => 42
        ];

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $httpClient->setParameterGet($query);

        foreach ($query as $key => $value) {
            $this->assertEquals($value, $zendClient->getRequest()->getQuery($key));
        }
    }

    /**
     * @test
     */
    public function sendRequestError()
    {
        $eMsg = 'Unknown Foo';
        $this->expectException(HttpRequestException::class);
        $this->expectExceptionMessage($eMsg);

        $zendClient = $this->getMockBuilder(\Zend\Http\Client::class)
            ->setMethods(['send', 'setMethod'])
            ->getMock();
        $zendClient
            ->expects($this->once())
            ->method('send')
            ->willThrowException(new \Zend\Http\Exception\RuntimeException($eMsg));

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $httpClient->send('FOO');
    }

    /**
     * @test
     */
    public function sendResponseError()
    {
        $eMsg = 'Unknown Foo';
        $this->expectException(HttpResponseException::class);
        $this->expectExceptionMessage($eMsg);

        $response = $this->getMockBuilder(\Zend\Http\Response::class)
            ->setMethods(['isSuccess', 'getBody'])
            ->getMock();
        $response
            ->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($eMsg);

        $zendClient = $this->getMockBuilder(\Zend\Http\Client::class)
            ->setMethods(['send', 'setMethod'])
            ->getMock();
        $zendClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $httpClient->send('FOO');
    }

    /**
     * @test
     */
    public function sendSuccess()
    {
        $responseBody = '{"hooray": true}';

        $response = $this->getMockBuilder(\Zend\Http\Response::class)
            ->setMethods(['isSuccess', 'getBody'])
            ->getMock();
        $response
            ->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($responseBody);

        $zendClient = $this->getMockBuilder(\Zend\Http\Client::class)
            ->setMethods(['send', 'setMethod'])
            ->getMock();
        $zendClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        /** @var HttpClient $httpClient */
        $httpClient = $this->objectManager->create(HttpClient::class, ['client' => $zendClient]);
        $this->assertEquals($responseBody, $httpClient->send('FOO'));
    }
}
