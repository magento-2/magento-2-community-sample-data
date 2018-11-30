<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\TaxAreaLookupResultInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see TaxAreaLookupResultInterface}
 *
 * @api
 */
interface TaxAreaLookupResultMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see TaxAreaLookupResultInterface}
     *
     * @param \stdClass $map
     * @return TaxAreaLookupResultInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see TaxAreaLookupResultInterface} into a SOAP compatible object
     *
     * @param TaxAreaLookupResultInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(TaxAreaLookupResultInterface $object);
}
