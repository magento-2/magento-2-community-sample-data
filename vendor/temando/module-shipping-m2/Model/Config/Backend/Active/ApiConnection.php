<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Config\Backend\Active;

use Magento\Framework\HTTP\Client\Curl;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Rest\Request\AuthRequestInterfaceFactory;

/**
 * Simple http client for testing API authentication.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ApiConnection
{
    /**
     * @var Curl
     */
    private $httpClient;

    /**
     * @var AuthRequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * ApiConnection constructor.
     *
     * @param Curl $httpClient
     * @param AuthRequestInterfaceFactory $requestFactory
     */
    public function __construct(
        Curl $httpClient,
        AuthRequestInterfaceFactory $requestFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Check if credentials are valid. Dismiss response.
     *
     * @param string $endpoint
     * @param string $accountId
     * @param string $bearerToken
     *
     * @return bool
     */
    public function test($endpoint, $accountId, $bearerToken)
    {
        $this->httpClient->setHeaders([
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/vnd.api+json',
            'Accept'        => 'application/vnd.api+json',
        ]);

        $request = $this->requestFactory->create([
            'scope' => AuthenticationInterface::AUTH_SCOPE_ADMIN,
            'accountId' => $accountId,
            'bearerToken' => $bearerToken,
        ]);

        $this->httpClient->post("$endpoint/sessions", $request->getRequestBody());

        return ($this->httpClient->getStatus() === 201);
    }
}
