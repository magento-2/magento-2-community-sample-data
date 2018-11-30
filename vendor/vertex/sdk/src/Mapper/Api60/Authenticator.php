<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\LoginInterface;
use Vertex\Mapper\AuthenticatorInterface;

/**
 * API Level 60 implementation of {@see AuthenticatorInterface}
 */
class Authenticator implements AuthenticatorInterface
{
    /** @var LoginMapper */
    private $loginMapper;

    /**
     * @param LoginMapper|null $loginMapper
     */
    public function __construct(LoginMapper $loginMapper = null)
    {
        $this->loginMapper = $loginMapper ?: new LoginMapper();
    }

    /**
     * @inheritdoc
     */
    public function addLogin(\stdClass $map, LoginInterface $login)
    {
        $map->Login = $this->loginMapper->map($login);
        return $map;
    }
}
