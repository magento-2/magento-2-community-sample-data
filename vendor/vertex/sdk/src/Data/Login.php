<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Default implementation of {@see LoginInterface}
 */
class Login implements LoginInterface
{
    /** @var string Password for authentication with Vertex */
    private $password;

    /** @var string Trusted ID for authentication with Vertex */
    private $trustedId;

    /** @var string Username for authentication with Vertex */
    private $username;

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getTrustedId()
    {
        return $this->trustedId;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTrustedId($trustedId)
    {
        $this->trustedId = $trustedId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
}
