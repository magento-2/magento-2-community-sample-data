<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\JurisdictionInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see JurisdictionInterface}
 *
 * @api
 */
interface JurisdictionMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see JurisdictionInterface}
     *
     * @param \stdClass $map
     * @return JurisdictionInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see JurisdictionInterface} into a SOAP compatible object
     *
     * @param JurisdictionInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(JurisdictionInterface $object);
}
