<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Services\TaxAreaLookup;

use Vertex\Data\TaxAreaLookupResultInterface;

/**
 * Default implementation of ResponseInterface
 */
class Response implements ResponseInterface
{
    /** @var TaxAreaLookupResultInterface[] */
    private $results = [];

    /**
     * @inheritdoc
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @inheritdoc
     */
    public function setResults(array $results)
    {
        array_walk(
            $results,
            function ($result) {
                if (!($result instanceof TaxAreaLookupResultInterface)) {
                    throw new \InvalidArgumentException(
                        'Lookup results must be instances of TaxAreaLookupResultInterface'
                    );
                }
            }
        );
        $this->results = $results;
        return $this;
    }
}
