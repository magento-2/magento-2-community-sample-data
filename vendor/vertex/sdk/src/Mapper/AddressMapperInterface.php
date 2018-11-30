<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\AddressInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see AddressInterface}
 *
 * @api
 */
interface AddressMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see AddressInterface}
     *
     * @param \stdClass $map
     * @return AddressInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see AddressInterface} into a SOAP compatible format
     *
     * @param AddressInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(AddressInterface $object);
}
