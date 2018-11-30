<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\TaxInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see TaxInterface}
 *
 * @api
 */
interface TaxMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see TaxInterface}
     *
     * @param \stdClass $map
     * @return TaxInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see TaxInterface} into a SOAP compatible object
     *
     * @param TaxInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(TaxInterface $object);
}
