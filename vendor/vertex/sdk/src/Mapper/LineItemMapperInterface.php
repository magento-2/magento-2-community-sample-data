<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\LineItemInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see LineItemInterface}
 *
 * @api
 */
interface LineItemMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see LineItemInterface}
     *
     * @param \stdClass $map
     * @return LineItemInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see LineItemInterface} into a SOAP compatible object
     *
     * @param LineItemInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(LineItemInterface $object);
}
