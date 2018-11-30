<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Exception\ValidationException;
use Vertex\Services\TaxAreaLookup\ResponseInterface;

/**
 * SOAP mapping methods for {@see ResponseInterface}
 *
 * @api
 */
interface TaxAreaLookupResponseMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see ResponseInterface}
     *
     * @param \stdClass $map
     * @return ResponseInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see ResponseInterface} into a SOAP compatible object
     *
     * @param ResponseInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(ResponseInterface $object);
}
