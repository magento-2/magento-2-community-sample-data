<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;

/**
 * Interface CreditApiInterface
 *
 * @package Klarna\Kp\Api
 */
interface CreditApiInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function createSession(RequestInterface $request);

    /**
     * @param string           $sessionId
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function updateSession($sessionId, RequestInterface $request);

    /**
     * @param string           $sessionId
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function readSession($sessionId);

    /**
     * @param string           $authorizationToken
     * @param RequestInterface $request
     * @param null             $klarnaId
     * @return ResponseInterface
     */
    public function placeOrder($authorizationToken, RequestInterface $request, $klarnaId = null);

    /**
     * @param string $authorizationToken
     * @param null   $klarnaId
     * @return ResponseInterface
     */
    public function cancelOrder($authorizationToken, $klarnaId = null);
}
