<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\LoginInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see LoginInterface}
 *
 * @api
 */
interface LoginMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see LoginInterface}
     *
     * @param \stdClass $map
     * @return LoginInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see LoginInterface} into a SOAP compatible object
     *
     * @param LoginInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(LoginInterface $object);
}
