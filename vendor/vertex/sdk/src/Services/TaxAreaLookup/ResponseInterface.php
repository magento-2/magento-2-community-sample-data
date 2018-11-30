<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Services\TaxAreaLookup;

use Vertex\Data\TaxAreaLookupResultInterface;

/**
 * Contains the response of a tax area lookup
 *
 * @api
 */
interface ResponseInterface
{
    /**
     * Get Lookup Results
     *
     * @return TaxAreaLookupResultInterface[]
     */
    public function getResults();

    /**
     * Set Lookup Results
     *
     * @param TaxAreaLookupResultInterface[] $results
     * @return ResponseInterface
     */
    public function setResults(array $results);
}
