<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Contains all information necessary for authenticating with Vertex
 *
 * @api
 */
interface LoginInterface
{
    /**
     * Get the Password
     *
     * @return string|null
     */
    public function getPassword();

    /**
     * Get the TrustedID
     *
     * @return string|null
     */
    public function getTrustedId();

    /**
     * Get the Username
     *
     * @return string|null
     */
    public function getUsername();


    /**
     * Set the Password
     *
     * @param string $password
     * @return LoginInterface
     */
    public function setPassword($password);

    /**
     * Set the TrustedID
     *
     * @param string $trustedId
     * @return LoginInterface
     */
    public function setTrustedId($trustedId);

    /**
     * Set the Username
     *
     * @param string $username
     * @return LoginInterface
     */
    public function setUsername($username);
}
