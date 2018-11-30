<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

/**
 * Temando API Session Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class AuthRequest implements AuthRequestInterface
{
    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $bearerToken;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * AuthRequest constructor.
     * @param string $scope
     * @param string $accountId
     * @param string $bearerToken
     * @param string $username
     * @param string $password
     */
    public function __construct($scope, $accountId = '', $bearerToken = '', $username = '', $password = '')
    {
        $this->scope       = $scope;
        $this->accountId   = $accountId;
        $this->bearerToken = $bearerToken;
        $this->username    = $username;
        $this->password    = $password;
    }

    /**
     * @return string
     */
    public function getRequestBody()
    {
        $attributes = [
            'scope' => $this->scope,
        ];

        if ($this->username && $this->password) {
            $attributes['email']    = $this->username;
            $attributes['password'] = $this->password;
        } else {
            $attributes['accountId']   = $this->accountId;
            $attributes['bearerToken'] = $this->bearerToken;
        }

        $params = [
            'data' => [
                'type' => 'session',
                'attributes' => $attributes,
            ],
        ];

        return json_encode($params);
    }
}
