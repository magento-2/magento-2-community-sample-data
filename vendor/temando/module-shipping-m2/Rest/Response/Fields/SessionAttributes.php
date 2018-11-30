<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields;

/**
 * Temando API Authentication Resource Object Attributes
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class SessionAttributes
{
    /**
     * @var string
     */
    private $sessionToken;

    /**
     * @var string
     */
    private $expiry;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @return string
     */
    public function getSessionToken()
    {
        return $this->sessionToken;
    }

    /**
     * @param string $sessionToken
     * @return void
     */
    public function setSessionToken($sessionToken)
    {
        $this->sessionToken = $sessionToken;
    }

    /**
     * @return string
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * @param string $expiry
     * @return void
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param string $apiUrl
     * @return void
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }
}
