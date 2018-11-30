<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Data\LoginInterface;
use Vertex\Exception\ValidationException;

/**
 * Add a login to an API Call
 *
 * @api
 */
interface AuthenticatorInterface
{
    /**
     * Add a login to an already created API call
     *
     * The passed in map must be a full service call, such as a result from {@see TaxAreaLookupRequestMapper::map()}.
     *
     * Well, not _technically_; but really only if you want this to actually work.
     *
     * From a technical standpoint, this expects that what we're building is the content of a Vertex Envelope and that
     * the map being returned from this method will be the top level object of a SOAP call.
     *
     * @param \stdClass $map
     * @param LoginInterface $login
     * @return \stdClass
     * @throws ValidationException
     */
    public function addLogin(\stdClass $map, LoginInterface $login);
}
