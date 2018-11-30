<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Data\Login;
use Vertex\Data\LoginInterface;
use Vertex\Mapper\LoginMapperInterface;
use Vertex\Mapper\MapperUtilities;

/**
 * API Level 60 implementation of {@see LoginMapperInterface}
 */
class LoginMapper implements LoginMapperInterface
{
    /**
     * Maximum amount of characters allowed for Trusted Id
     */
    const TRUSTED_ID_MAX = 16;

    /**
     * Minimum amount of characters allowed for Trusted Id
     */
    const TRUSTED_ID_MIN = 6;

    /** @var MapperUtilities */
    private $utilities;

    /**
     * @param MapperUtilities|null $utilities
     */
    public function __construct(MapperUtilities $utilities = null)
    {
        $this->utilities = $utilities ?: new MapperUtilities();
    }

    /**
     * @inheritdoc
     */
    public function build(\stdClass $map)
    {
        $login = new Login();
        if (isset($map->TrustedId)) {
            $login->setTrustedId((string)$map->TrustedId);
        }

        return $login;
    }

    /**
     * @inheritdoc
     */
    public function map(LoginInterface $object)
    {
        $map = new \stdClass();
        $map = $this->utilities->addToMapWithLengthValidation(
            $map,
            $object->getTrustedId(),
            'TrustedId',
            static::TRUSTED_ID_MIN,
            static::TRUSTED_ID_MAX,
            true,
            'Trusted ID'
        );
        return $map;
    }
}
