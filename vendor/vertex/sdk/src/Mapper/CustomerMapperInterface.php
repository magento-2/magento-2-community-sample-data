<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\CustomerInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see CustomerInterface}
 *
 * @api
 */
interface CustomerMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see CustomerInterface}
     *
     * @param \stdClass $map
     * @return CustomerInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see CustomerInterface} into a SOAP compatible object
     *
     * @param CustomerInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(CustomerInterface $object);
}
