<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper\Api60;

use Vertex\Mapper\TaxAreaLookupResponseMapperInterface;
use Vertex\Mapper\TaxAreaLookupResultMapperInterface;
use Vertex\Services\TaxAreaLookup\Response;
use Vertex\Services\TaxAreaLookup\ResponseInterface;

/**
 * API Level 60 implementation of {@see TaxAreaLookupResponseMapperInterface}
 */
class TaxAreaLookupResponseMapper implements TaxAreaLookupResponseMapperInterface
{
    /** @var TaxAreaLookupResultMapperInterface */
    private $resultMapper;

    /**
     * @param TaxAreaLookupResultMapperInterface|null $resultMapper
     */
    public function __construct(TaxAreaLookupResultMapperInterface $resultMapper = null)
    {
        $this->resultMapper = $resultMapper ?: new TaxAreaLookupResultMapper();
    }

    /**
     * @inheritdoc
     */
    public function build(\stdClass $map)
    {
        $response = new Response();

        $mapResults = $map->TaxAreaResponse->TaxAreaResult;
        if (!is_array($mapResults)) {
            $mapResults = [$mapResults];
        }

        $results = [];
        foreach ($mapResults as $mapResult) {
            $results[] = $this->resultMapper->build($mapResult);
        }

        $response->setResults($results);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function map(ResponseInterface $object)
    {
        $map = new \stdClass();
        $map->TaxAreaResponse = new \stdClass();
        $map->TaxAreaResponse->TaxAreaResult = [];
        $lookupResults = $object->getResults();
        foreach ($lookupResults as $lookupResult) {
            $map->TaxAreaResponse->TaxAreaResult[] = $this->resultMapper->map($lookupResult);
        }

        return $map;
    }
}
